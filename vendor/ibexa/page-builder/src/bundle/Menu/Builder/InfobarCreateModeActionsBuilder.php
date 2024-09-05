<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\PageBuilder\Menu\Builder;

use Ibexa\AdminUi\Menu\MenuItemFactory;
use Ibexa\Bundle\PageBuilder\Menu\Event\PageBuilderConfigureMenuEventName;
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
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * KnpMenuBundle Menu Builder service implementation.
 *
 * @see https://symfony.com/doc/current/bundles/KnpMenuBundle/menu_builder_service.html
 */
class InfobarCreateModeActionsBuilder extends AbstractBuilder implements TranslationContainerInterface
{
    /* Menu items */
    public const ITEM__PUBLISH = 'page_builder__infobar__create__actions__publish';
    public const ITEM__SAVE_DRAFT = 'page_builder__infobar__create__actions__save_draft';
    public const ITEM__CANCEL = 'page_builder__infobar__create__actions__cancel';

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $locationService;

    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    /**
     * @param \Ibexa\AdminUi\Menu\MenuItemFactory
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface
     * @param \Ibexa\Contracts\Core\Repository\PermissionResolver
     * @param \Ibexa\Contracts\Core\Repository\LocationService
     * @param \Symfony\Contracts\Translation\TranslatorInterface
     */
    public function __construct(
        MenuItemFactory $factory,
        EventDispatcherInterface $eventDispatcher,
        PermissionResolver $permissionResolver,
        ContentService $contentService,
        LocationService $locationService,
        TranslatorInterface $translator
    ) {
        parent::__construct($factory, $eventDispatcher);

        $this->permissionResolver = $permissionResolver;
        $this->contentService = $contentService;
        $this->locationService = $locationService;
        $this->translator = $translator;
    }

    /**
     * @return string
     */
    protected function getConfigureEventName(): string
    {
        return PageBuilderConfigureMenuEventName::PAGE_BUILDER_INFOBAR_CREATE_MODE_ACTIONS;
    }

    /**
     * @param array $options
     *
     * @return \Knp\Menu\ItemInterface
     *
     * @throws \InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function createStructure(array $options): ItemInterface
    {
        /** @var \Knp\Menu\ItemInterface|\Knp\Menu\ItemInterface[] $menu */
        $menu = $this->factory->createItem(
            'root',
            [
                'extras' => [
                    'adaptiveItemsSelectorBtnClass' => 'btn ibexa-btn ibexa-btn--no-text ibexa-btn--secondary',
                ],
            ]
        );
        $menuItems = [];

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location $location */
        $location = $options['parent_location'];
        /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType */
        $contentType = $options['content_type'];
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Language $language */
        $language = $options['language'];

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\ContentCreateStruct $contentCreateStruct */
        $contentCreateStruct = $this->createContentCreateStruct($location, $contentType, $language);

        if ($this->canContentBePublished($contentCreateStruct, $location)) {
            $menuItems[self::ITEM__PUBLISH] = $this->createMenuItem(
                self::ITEM__PUBLISH,
                [
                    'attributes' => [
                        'class' => 'ibexa-btn--trigger',
                        'data-click' => '#ezplatform_content_forms_content_edit_publish',
                    ],
                    'extras' => [
                        'orderNumber' => 20,
                    ],
                ]
            );
        }

        if ($this->canContentBeEdited($location)) {
            $menuItems[self::ITEM__SAVE_DRAFT] = $this->createMenuItem(
                self::ITEM__SAVE_DRAFT,
                [
                    'attributes' => [
                        'class' => 'ibexa-btn--info ibexa-btn--trigger',
                        'data-click' => '#ezplatform_content_forms_content_edit_saveDraft',
                    ],
                    'extras' => [
                        'orderNumber' => 30,
                        'noDefaultBtnStyling' => true,
                        'adaptiveItemsForceHide' => true,
                    ],
                ]
            );
        }

        if ($this->canRemoveVersion($location)) {
            $menuItems[self::ITEM__CANCEL] = $this->createMenuItem(
                self::ITEM__CANCEL,
                [
                    'attributes' => [
                        'class' => 'ibexa-btn--dark ibexa-btn--trigger',
                        'data-click' => '#ezplatform_content_forms_content_edit_cancel',
                    ],
                    'extras' => [
                        'orderNumber' => 10,
                        'noDefaultBtnStyling' => true,
                    ],
                ]
            );
        }

        $menu->setChildren($menuItems);

        return $menu;
    }

    /**
     * @return \JMS\TranslationBundle\Model\Message[]
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM__PUBLISH, 'ibexa_menu'))->setDesc('Publish'),
            (new Message(self::ITEM__SAVE_DRAFT, 'ibexa_menu'))->setDesc('Save draft'),
            (new Message(self::ITEM__CANCEL, 'ibexa_menu'))->setDesc('Delete draft'),
        ];
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\ContentCreateStruct $contentCreateStruct
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $location
     *
     * @return bool
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    private function canContentBePublished(ContentCreateStruct $contentCreateStruct, Location $location): bool
    {
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\LocationCreateStruct */
        $locationCreateStruct = $this->locationService->newLocationCreateStruct($location->id);

        $canPublishContent = $this->permissionResolver->canUser(
            'content',
            'publish',
            $contentCreateStruct,
            [$locationCreateStruct]
        );

        $canCreateContent = $this->permissionResolver->canUser(
            'content',
            'create',
            $contentCreateStruct,
            [$locationCreateStruct]
        );

        return $canPublishContent && $canCreateContent;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $location
     *
     * @return bool
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    private function canContentBeEdited(Location $location): bool
    {
        return $this->permissionResolver->canUser(
            'content',
            'edit',
            $location->getContentInfo(),
            [$location]
        );
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $location
     *
     * @return bool
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    private function canRemoveVersion(Location $location): bool
    {
        return $this->permissionResolver->canUser(
            'content',
            'versionremove',
            $location->getContentInfo(),
            [$location]
        );
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $location
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Language $language
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\ContentCreateStruct
     */
    private function createContentCreateStruct(Location $location, ContentType $contentType, Language $language): ContentCreateStruct
    {
        $contentCreateStruct = $this->contentService->newContentCreateStruct($contentType, $language->languageCode);
        $contentCreateStruct->sectionId = $location->contentInfo->sectionId;

        return $contentCreateStruct;
    }
}

class_alias(InfobarCreateModeActionsBuilder::class, 'EzSystems\EzPlatformPageBuilderBundle\Menu\Builder\InfobarCreateModeActionsBuilder');
