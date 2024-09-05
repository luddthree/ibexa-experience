<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ActivityLog\Event\Menu;

use Ibexa\ActivityLog\Permission\PolicyProvider;
use Ibexa\AdminUi\Menu\Event\ConfigureMenuEvent;
use Ibexa\AdminUi\Menu\MainMenuBuilder;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class MainMenuSubscriber implements EventSubscriberInterface, TranslationContainerInterface
{
    public const ITEM_ACTIVITY_LOG_LIST = 'main__activity_log_list';

    private PermissionResolver $permissionResolver;

    public function __construct(PermissionResolver $permissionResolver)
    {
        $this->permissionResolver = $permissionResolver;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ConfigureMenuEvent::MAIN_MENU => ['onConfigureMainMenu'],
        ];
    }

    public function onConfigureMainMenu(ConfigureMenuEvent $event): void
    {
        $menu = $event->getMenu();

        $adminMenu = $menu->getChild(MainMenuBuilder::ITEM_ADMIN);
        if ($adminMenu === null) {
            return;
        }

        if ($this->permissionResolver->hasAccess(PolicyProvider::MODULE_ACTIVITY_LOG, 'read') === false) {
            return;
        }

        $adminMenu->addChild(self::ITEM_ACTIVITY_LOG_LIST, [
            'route' => 'ibexa.activity_log.list',
            'extras' => [
                'orderNumber' => 70,
            ],
        ]);
    }

    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM_ACTIVITY_LOG_LIST, 'ibexa_menu'))->setDesc('Recent activity'),
        ];
    }
}
