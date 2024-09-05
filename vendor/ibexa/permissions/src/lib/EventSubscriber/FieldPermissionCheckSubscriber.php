<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Permissions\EventSubscriber;

use Ibexa\ContentForms\Event\ContentCreateFieldOptionsEvent;
use Ibexa\ContentForms\Event\ContentFormEvents;
use Ibexa\ContentForms\Event\ContentUpdateFieldOptionsEvent;
use Ibexa\ContentForms\Event\UserCreateFieldOptionsEvent;
use Ibexa\ContentForms\Event\UserUpdateFieldOptionsEvent;
use Ibexa\Contracts\Core\FieldType\Value;
use Ibexa\Contracts\Core\Limitation\Target;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Core\FieldType\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FieldPermissionCheckSubscriber implements EventSubscriberInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $locationService;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    private User\Type $userType;

    public function __construct(
        ContentService $contentService,
        LocationService $locationService,
        PermissionResolver $permissionResolver,
        User\Type $userType
    ) {
        $this->contentService = $contentService;
        $this->locationService = $locationService;
        $this->permissionResolver = $permissionResolver;
        $this->userType = $userType;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ContentFormEvents::CONTENT_EDIT_FIELD_OPTIONS => 'onContentEditFieldOptions',
            ContentFormEvents::CONTENT_CREATE_FIELD_OPTIONS => 'onContentCreateFieldOptions',
            ContentFormEvents::USER_EDIT_FIELD_OPTIONS => 'onUserEditFieldOptions',
            ContentFormEvents::USER_CREATE_FIELD_OPTIONS => 'onUserCreateFieldOptions',
        ];
    }

    public function onContentCreateFieldOptions(ContentCreateFieldOptionsEvent $event)
    {
        $contentCreateStruct = clone $event->getContentCreateStruct();
        $contentField = $event->getFieldData()->field;

        $contentCreateStruct->fields = [
            new Field([
                'id' => $contentField->id,
                'fieldDefIdentifier' => $contentField->fieldDefIdentifier,
                'fieldTypeIdentifier' => $contentField->fieldTypeIdentifier,
                'languageCode' => $contentField->languageCode,
                'value' => new class() implements Value {
                    public function __get(string $property)
                    {
                        return uniqid($property);
                    }

                    public function __toString(): string
                    {
                        return 'non-empty-value';
                    }
                },
            ]),
        ];

        $locationCreateStructs = $contentCreateStruct->getLocationStructs();

        if (empty($contentCreateStruct->sectionId)) {
            if (isset($locationCreateStructs[0])) {
                $location = $this->locationService->loadLocation(
                    $locationCreateStructs[0]->parentLocationId
                );
                $contentCreateStruct->sectionId = $location->contentInfo->sectionId;
            } else {
                $contentCreateStruct->sectionId = 1;
            }
        }

        if (!$this->permissionResolver->canUser('content', 'create', $contentCreateStruct, $locationCreateStructs)) {
            $event->setOption('disabled', true);
        }
    }

    public function onContentEditFieldOptions(ContentUpdateFieldOptionsEvent $event): void
    {
        $updatedFields = [
            $event->getFieldData()->field,
        ];

        $content = $event->getContent();
        $contentUpdateStruct = clone $event->getContentUpdateStruct();
        $contentUpdateStruct->fields = $updatedFields;

        if (!$this->permissionResolver->canUser(
            'content',
            'edit',
            $content,
            [
                (new Target\Builder\VersionBuilder())
                    ->updateFields($updatedFields)
                    ->updateFieldsTo(
                        $contentUpdateStruct->initialLanguageCode,
                        $contentUpdateStruct->fields
                    )
                    ->build(),
            ]
        )) {
            $event->setOption('disabled', true);
        }
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     */
    public function onUserCreateFieldOptions(UserCreateFieldOptionsEvent $event): void
    {
        $userCreateStruct = clone $event->getUserCreateStruct();
        $contentField = $event->getFieldData()->field;

        $userCreateStruct->fields = [
            new Field([
                'id' => $contentField->id,
                'fieldDefIdentifier' => $contentField->fieldDefIdentifier,
                'fieldTypeIdentifier' => $contentField->fieldTypeIdentifier,
                'languageCode' => $contentField->languageCode,
                'value' => $this->userType->getEmptyValue(),
            ]),
        ];

        if (!$this->permissionResolver->canUser('content', 'create', $userCreateStruct)) {
            $event->setOption('disabled', true);
        }
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     */
    public function onUserEditFieldOptions(UserUpdateFieldOptionsEvent $event): void
    {
        $updatedFields = [
            $event->getFieldData()->field,
        ];

        if (!$this->userCanEditContentField($event, $updatedFields)) {
            $event->setOption('disabled', true);
        }
    }

    /**
     * @param array<\Ibexa\Contracts\Core\Repository\Values\Content\Field> $fields
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     */
    private function userCanEditContentField(UserUpdateFieldOptionsEvent $event, array $fields): bool
    {
        return $this->permissionResolver->canUser(
            'content',
            'edit',
            $event->getContent(),
            [
                (new Target\Builder\VersionBuilder())
                    ->updateFields($fields)
                    ->updateFieldsTo(
                        $event->getOption('languageCode'),
                        $fields,
                    )
                    ->build(),
            ]
        );
    }
}

class_alias(FieldPermissionCheckSubscriber::class, 'Ibexa\Platform\Permissions\EventSubscriber\FieldPermissionCheckSubscriber');
