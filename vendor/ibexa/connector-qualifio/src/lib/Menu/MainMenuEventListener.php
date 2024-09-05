<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ConnectorQualifio\Menu;

use Ibexa\AdminUi\Menu\Event\ConfigureMenuEvent;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class MainMenuEventListener implements EventSubscriberInterface, TranslationContainerInterface
{
    public const ITEM_INTEGRATION__ENGAGE_CAMPAIGN = 'main__integration__engage_campaign';

    public static function getSubscribedEvents(): array
    {
        return [
            ConfigureMenuEvent::MAIN_MENU => [
                ['addMenu', -45],
            ],
        ];
    }

    public function addMenu(ConfigureMenuEvent $event): void
    {
        $menu = $event->getMenu();
        $menu->addChild(
            self::ITEM_INTEGRATION__ENGAGE_CAMPAIGN,
            [
                'route' => 'ibexa.qualifio.index',
                'attributes' => [
                    'data-tooltip-placement' => 'right',
                    'data-tooltip-extra-class' => 'ibexa-tooltip--navigation',
                ],
                'extras' => [
                    'icon' => 'campaign',
                    'orderNumber' => 135,
                ],
            ],
        );
    }

    public static function getTranslationMessages(): array
    {
        return [
            Message::create(self::ITEM_INTEGRATION__ENGAGE_CAMPAIGN, 'ibexa_menu')->setDesc('Ibexa Engage'),
        ];
    }
}
