<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Dashboard\EventSubscriber;

use Ibexa\AdminUi\Menu\Event\ConfigureMenuEvent;
use Ibexa\AdminUi\Menu\MainMenuBuilder;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class MainMenuSubscriber implements EventSubscriberInterface, TranslationContainerInterface
{
    // Main Menu / Dashboard
    public const ITEM_CUSTOMIZABLE_DASHBOARD = 'main__customizable_dashboard';

    public static function getSubscribedEvents(): array
    {
        return [
            ConfigureMenuEvent::MAIN_MENU => ['onConfigureMainMenu'],
        ];
    }

    public function onConfigureMainMenu(ConfigureMenuEvent $event): void
    {
        $menu = $event->getMenu();

        $menu->addChild(
            self::ITEM_CUSTOMIZABLE_DASHBOARD,
            [
                'route' => 'ibexa.dashboard',
                'attributes' => [
                    'data-tooltip-placement' => 'right',
                    'data-tooltip-extra-class' => 'ibexa-tooltip--navigation',
                ],
                'extras' => [
                    'icon' => 'home-page',
                    'orderNumber' => 0,
                ],
            ]
        );

        $contentMenu = $menu->getChild(MainMenuBuilder::ITEM_CONTENT);
        if ($contentMenu !== null) {
            $contentMenu->removeChild(MainMenuBuilder::ITEM_DASHBOARD);
        }
    }

    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM_CUSTOMIZABLE_DASHBOARD, 'ibexa_menu'))->setDesc('Dashboard'),
        ];
    }
}
