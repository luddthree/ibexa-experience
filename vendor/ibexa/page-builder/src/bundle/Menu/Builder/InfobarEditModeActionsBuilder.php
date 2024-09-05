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
use Ibexa\Contracts\Core\Limitation\Target;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
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
class InfobarEditModeActionsBuilder extends AbstractBuilder implements TranslationContainerInterface
{
    /* Menu items */
    public const ITEM__PUBLISH = 'page_builder__infobar__edit__actions__publish';
    public const ITEM__SAVE_DRAFT = 'page_builder__infobar__edit__actions__save_draft';
    public const ITEM__SAVE_DRAFT_AND_CLOSE = 'page_builder__infobar__edit__actions__save_draft_and_close';
    public const ITEM__CANCEL = 'page_builder__infobar__edit__actions__cancel';

    private const BTN_TRIGGER_CLASS = 'ibexa-btn--trigger';

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

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
        LocationService $locationService,
        TranslatorInterface $translator
    ) {
        parent::__construct($factory, $eventDispatcher);

        $this->permissionResolver = $permissionResolver;
        $this->locationService = $locationService;
        $this->translator = $translator;
    }

    /**
     * @return string
     */
    protected function getConfigureEventName(): string
    {
        return PageBuilderConfigureMenuEventName::PAGE_BUILDER_INFOBAR_EDIT_MODE_ACTIONS;
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

        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $content */
        $content = $options['content'];
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location|null $location */
        $location = $options['location'] ?? null;
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Language|null $language */
        $language = $options['language'] ?? null;

        if ($this->canContentBePublished($content, $location, $language)) {
            $menuItems[self::ITEM__PUBLISH] = $this->createMenuItem(
                self::ITEM__PUBLISH,
                [
                    'attributes' => [
                        'class' => self::BTN_TRIGGER_CLASS,
                        'data-click' => '#ezplatform_content_forms_content_edit_publish',
                    ],
                    'extras' => [
                        'orderNumber' => 10,
                    ],
                ]
            );
        }

        if ($this->canContentBeEdited($content, $location, $language)) {
            $saveDraftAndCloseItem = $this->createMenuItem(
                self::ITEM__SAVE_DRAFT_AND_CLOSE,
                [
                    'attributes' => [
                        'class' => self::BTN_TRIGGER_CLASS,
                        'data-click' => '#ezplatform_content_forms_content_edit_saveDraftAndClose',
                    ],
                    'extras' => [
                        'orderNumber' => 30,
                    ],
                ]
            );

            $saveDraftAndCloseItem->addChild(
                self::ITEM__SAVE_DRAFT,
                [
                    'attributes' => [
                        'class' => self::BTN_TRIGGER_CLASS,
                        'data-click' => '#ezplatform_content_forms_content_edit_saveDraft',
                    ],
                    'extras' => [
                        'orderNumber' => 10,
                    ],
                ]
            );

            $menuItems[self::ITEM__SAVE_DRAFT_AND_CLOSE] = $saveDraftAndCloseItem;
        }

        if ($this->canRemoveVersion($content, $location)) {
            $menuItems[self::ITEM__CANCEL] = $this->createMenuItem(
                self::ITEM__CANCEL,
                [
                    'attributes' => [
                        'class' => self::BTN_TRIGGER_CLASS,
                        'data-click' => '#ezplatform_content_forms_content_edit_cancel',
                    ],
                    'extras' => [
                        'orderNumber' => 20,
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
            (new Message(self::ITEM__SAVE_DRAFT, 'ibexa_menu'))->setDesc('Save'),
            (new Message(self::ITEM__CANCEL, 'ibexa_menu'))->setDesc('Delete draft'),
            (new Message(self::ITEM__SAVE_DRAFT_AND_CLOSE, 'ibexa_menu'))->setDesc('Save and close'),
        ];
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location|null $location
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Language $language|null $language
     *
     * @return bool
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    private function canContentBePublished(Content $content, ?Location $location, ?Language $language): bool
    {
        $targets = [];

        if (null !== $language) {
            $targets[] = (new Target\Builder\VersionBuilder())
                ->changeStatusTo(VersionInfo::STATUS_PUBLISHED)
                ->updateFieldsTo($language->languageCode, [])
                ->build();
        }
        if (null !== $location) {
            $targets[] = $location;
        }

        $canPublishContent = $this->permissionResolver->canUser(
            'content',
            'publish',
            $content,
            $targets
        );

        $canEditContent = $this->permissionResolver->canUser(
            'content',
            'edit',
            $content,
            $targets
        );

        return $canPublishContent && $canEditContent;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location|null $location
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Language|null $language
     *
     * @return bool
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    private function canContentBeEdited(Content $content, ?Location $location, ?Language $language): bool
    {
        $targets = [];

        if (null !== $language) {
            $targets[] = (new Target\Builder\VersionBuilder())->translateToAnyLanguageOf([$language->languageCode])->build();
        }
        if (null !== $location) {
            $targets[] = $location;
        }

        return $this->permissionResolver->canUser(
            'content',
            'edit',
            $content,
            $targets
        );
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location|null $location
     *
     * @return bool
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    private function canRemoveVersion(Content $content, ?Location $location): bool
    {
        return $this->permissionResolver->canUser(
            'content',
            'versionremove',
            $content,
            $location ? [$location] : []
        );
    }
}

class_alias(InfobarEditModeActionsBuilder::class, 'EzSystems\EzPlatformPageBuilderBundle\Menu\Builder\InfobarEditModeActionsBuilder');
