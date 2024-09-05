<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Calendar\UI\Menu\Listener;

use Ibexa\AdminUi\Menu\Event\ConfigureMenuEvent;
use Ibexa\AdminUi\Menu\MainMenuBuilder;
use Ibexa\AdminUi\Menu\MenuItemFactory;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

final class ConfigureMainMenuListener implements TranslationContainerInterface
{
    public const ITEM_CONTENT__CALENDAR = 'main__content__calendar';

    /** @var \Ibexa\AdminUi\Menu\MenuItemFactory */
    private $menuItemFactory;

    public function __construct(MenuItemFactory $menuItemFactory)
    {
        $this->menuItemFactory = $menuItemFactory;
    }

    public function onMenuConfigure(ConfigureMenuEvent $event): void
    {
        $root = $event->getMenu();
        $root->getChild(MainMenuBuilder::ITEM_CONTENT)->addChild(
            $this->menuItemFactory->createItem(
                self::ITEM_CONTENT__CALENDAR,
                [
                    'route' => 'ibexa.calendar.view',
                    'extras' => [
                        'orderNumber' => 55,
                    ],
                ]
            )
        );
    }

    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM_CONTENT__CALENDAR, 'ibexa_menu'))->setDesc('Calendar'),
        ];
    }
}

class_alias(ConfigureMainMenuListener::class, 'EzSystems\EzPlatformCalendarBundle\UI\Menu\Listener\ConfigureMainMenuListener');
