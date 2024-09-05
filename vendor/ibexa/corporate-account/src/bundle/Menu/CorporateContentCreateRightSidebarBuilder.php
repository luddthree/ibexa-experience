<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\Menu;

use Ibexa\AdminUi\Menu\MenuItemFactory;
use Ibexa\Contracts\AdminUi\Menu\AbstractBuilder;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class CorporateContentCreateRightSidebarBuilder extends AbstractBuilder implements TranslationContainerInterface
{
    public const EVENT_NAME = 'ibexa_corporate_account.menu_configure.corporate_content_create_context_menu';

    public const ITEM__PUBLISH = 'corporate_content_create__sidebar_right__publish';
    public const ITEM__CANCEL = 'corporate_content_create__sidebar_right__cancel';

    public const BTN_TRIGGER_CLASS = 'ibexa-btn--trigger';
    public const BTN_DISABLED_ATTR = ['disabled' => 'disabled'];

    private PermissionResolver $permissionResolver;

    private ContentService $contentService;

    private LocationService $locationService;

    public function __construct(
        MenuItemFactory $factory,
        EventDispatcherInterface $eventDispatcher,
        PermissionResolver $permissionResolver,
        ContentService $contentService,
        LocationService $locationService
    ) {
        parent::__construct($factory, $eventDispatcher);

        $this->permissionResolver = $permissionResolver;
        $this->contentService = $contentService;
        $this->locationService = $locationService;
    }

    protected function getConfigureEventName(): string
    {
        return self::EVENT_NAME;
    }

    /**
     * @param array<string,mixed> $options
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function createStructure(array $options): ItemInterface
    {
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $parentLocation */
        $parentLocation = $options['parent_location'];
        /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType */
        $contentType = $options['content_type'];
        $parentContentType = $parentLocation->getContent()->getContentType();
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Language $language */
        $language = $options['language'];
        /** @var \Knp\Menu\ItemInterface|\Knp\Menu\ItemInterface[] $menu */
        $menu = $this->factory->createItem('root');

        $contentCreateStruct = $this->createContentCreateStruct($parentLocation, $contentType, $language);
        $locationCreateStruct = $this->locationService->newLocationCreateStruct($parentLocation->id);

        $canPublish = $this->permissionResolver->canUser('content', 'publish', $contentCreateStruct, [$locationCreateStruct]);
        $canCreate = $this->permissionResolver->canUser('content', 'create', $contentCreateStruct, [$locationCreateStruct]) && $parentContentType->isContainer;

        $publishAttributes = [
            'class' => self::BTN_TRIGGER_CLASS,
            'data-click' => '#ezplatform_content_forms_content_edit_publish',
        ];

        $menu->setChildren([
            self::ITEM__PUBLISH => $this->createMenuItem(
                self::ITEM__PUBLISH,
                [
                    'attributes' => $canCreate && $canPublish
                        ? $publishAttributes
                        : array_merge($publishAttributes, self::BTN_DISABLED_ATTR),
                    'extras' => [
                        'orderNumber' => 10,
                    ],
                ]
            ),
            self::ITEM__CANCEL => $this->createMenuItem(
                self::ITEM__CANCEL,
                [
                    'uri' => $options['cancel_uri'] ?? null,
                    'extras' => [
                        'orderNumber' => 30,
                    ],
                ]
            ),
        ]);

        return $menu;
    }

    /**
     * @return \JMS\TranslationBundle\Model\Message[]
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM__PUBLISH, 'ibexa_menu'))->setDesc('Save and close'),
            (new Message(self::ITEM__CANCEL, 'ibexa_menu'))->setDesc('Discard'),
        ];
    }

    private function createContentCreateStruct(Location $location, ContentType $contentType, Language $language): ContentCreateStruct
    {
        $contentCreateStruct = $this->contentService->newContentCreateStruct($contentType, $language->languageCode);
        $contentCreateStruct->sectionId = $location->contentInfo->sectionId;

        return $contentCreateStruct;
    }
}
