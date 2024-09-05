<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\Event\Subscriber;

use Ibexa\AdminUi\Menu\Event\ConfigureMenuEvent;
use Ibexa\AdminUi\Menu\MainMenuBuilder;
use Ibexa\AdminUi\Menu\MenuItemFactory;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConfigureMainMenuListener implements EventSubscriberInterface, TranslationContainerInterface
{
    public const ITEM_ADMIN_SEGMENTS = 'main__admin__segments';

    /** @var \Ibexa\AdminUi\Menu\MenuItemFactory */
    private $menuItemFactory;

    /**
     * @param \Ibexa\AdminUi\Menu\MenuItemFactory $menuItemFactory
     */
    public function __construct(
        MenuItemFactory $menuItemFactory
    ) {
        $this->menuItemFactory = $menuItemFactory;
    }

    /**
     * @param \Ibexa\AdminUi\Menu\Event\ConfigureMenuEvent $event
     */
    public function onMenuConfigure(ConfigureMenuEvent $event): void
    {
        $menu = $event->getMenu();

        if (!$adminMenu = $menu->getChild(MainMenuBuilder::ITEM_ADMIN)) {
            return;
        }

        $adminMenu->addChild($this->menuItemFactory->createItem(
            self::ITEM_ADMIN_SEGMENTS,
            [
                'route' => 'ibexa.segmentation.group.list',
                'extras' => [
                    'routes' => [
                        'segment_group_view' => 'ibexa.segmentation.group.view',
                        'segment_group_create' => 'ibexa.segmentation.group.create',
                        'segment_group_update' => 'ibexa.segmentation.group.update',
                        'segment_group_delete' => 'ibexa.segmentation.group.delete',
                        'segment_group_bulk_delete' => 'ibexa.segmentation.group.bulk_delete',
                        'segment_create' => 'ibexa.segmentation.segment.create',
                        'segment_update' => 'ibexa.segmentation.segment.update',
                        'segment_bulk_delete' => 'ibexa.segmentation.segment.bulk_delete',
                    ],
                    'orderNumber' => 50,
                ],
            ]
        ));
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ConfigureMenuEvent::MAIN_MENU => 'onMenuConfigure',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM_ADMIN_SEGMENTS, 'ibexa_menu'))->setDesc('Segments'),
        ];
    }
}

class_alias(ConfigureMainMenuListener::class, 'Ibexa\Platform\Segmentation\Event\Subscriber\ConfigureMainMenuListener');
