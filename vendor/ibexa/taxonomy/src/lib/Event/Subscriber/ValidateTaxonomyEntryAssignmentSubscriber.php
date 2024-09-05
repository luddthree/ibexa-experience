<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Event\Subscriber;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Events\Content\BeforeUpdateContentEvent;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Core\Base\Exceptions\UnauthorizedException;
use Ibexa\Core\FieldType\FieldType;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinition;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @internal
 */
final class ValidateTaxonomyEntryAssignmentSubscriber implements EventSubscriberInterface
{
    private ContentService $contentService;

    private FieldType $taxonomyEntryAssignmentFieldType;

    private PermissionResolver $permissionResolver;

    public function __construct(
        ContentService $contentService,
        FieldType $taxonomyEntryAssignmentFieldType,
        PermissionResolver $permissionResolver
    ) {
        $this->contentService = $contentService;
        $this->taxonomyEntryAssignmentFieldType = $taxonomyEntryAssignmentFieldType;
        $this->permissionResolver = $permissionResolver;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeUpdateContentEvent::class => 'onBeforeUpdateContent',
        ];
    }

    public function onBeforeUpdateContent(BeforeUpdateContentEvent $event): void
    {
        $versionInfo = $event->getVersionInfo();
        $contentUpdateStruct = $event->getContentUpdateStruct();
        $taxonomyEntryAssignmentFieldTypeIdentifier = $this->taxonomyEntryAssignmentFieldType->getFieldTypeIdentifier();
        $content = $this->contentService->loadContentByVersionInfo($versionInfo);
        $contentType = $content->getContentType();

        if (!$contentType->hasFieldDefinitionOfType($taxonomyEntryAssignmentFieldTypeIdentifier)) {
            return;
        }

        $taxonomyEntryAssignmentFieldDefinitions = $contentType->getFieldDefinitionsOfType(
            $taxonomyEntryAssignmentFieldTypeIdentifier
        );
        $taxonomyEntryAssignmentFieldDefIdentifiers = $taxonomyEntryAssignmentFieldDefinitions->map(
            static fn (FieldDefinition $field): string => $field->identifier,
        );

        foreach ($contentUpdateStruct->fields as $field) {
            $contentField = $content->getField($field->fieldDefIdentifier, $field->languageCode);

            if (
                null === $contentField
                || !in_array($field->fieldDefIdentifier, $taxonomyEntryAssignmentFieldDefIdentifiers, true)
                || $this->taxonomyEntryAssignmentFieldType->isEmptyValue($field->value)
            ) {
                continue;
            }

            /** @var \Ibexa\Taxonomy\FieldType\TaxonomyEntryAssignment\Value $originalFieldValue */
            $originalFieldValue = $contentField->value;

            /** @var \Ibexa\Taxonomy\FieldType\TaxonomyEntryAssignment\Value $updatedFieldValue */
            $updatedFieldValue = $field->value;
            $originalEntryIdentifiers = array_column($originalFieldValue->getTaxonomyEntries(), 'identifier');
            $updatedEntryIdentifiers = array_column($updatedFieldValue->getTaxonomyEntries(), 'identifier');

            $isFieldUpdated = $originalFieldValue->getTaxonomy() !== $updatedFieldValue->getTaxonomy()
                || $originalEntryIdentifiers != $updatedEntryIdentifiers;

            if ($isFieldUpdated && !$this->permissionResolver->hasAccess('taxonomy', 'assign')) {
                throw new UnauthorizedException('taxonomy', 'read', ['contentId' => $content->id]);
            }
        }
    }
}
