<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Dashboard\Menu;

use Ibexa\AdminUi\Menu\Event\ConfigureMenuEvent;
use Ibexa\AdminUi\Menu\MainMenuBuilder;
use Ibexa\AdminUi\Menu\MenuItemFactory;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;

final class ConfigureMainMenuListener implements TranslationContainerInterface
{
    public const ITEM_ADMIN__DASHBOARD = 'main__admin__dashboard';
    public const ITEM_ADMIN__DASHBOARD_CONTENT_TYPE = 'main__admin__dashboard_content_type';

    private MenuItemFactory $menuItemFactory;

    private ConfigResolverInterface $configResolver;

    private LocationService $locationService;

    public function __construct(
        MenuItemFactory $menuItemFactory,
        ConfigResolverInterface $configResolver,
        LocationService $locationService
    ) {
        $this->menuItemFactory = $menuItemFactory;
        $this->configResolver = $configResolver;
        $this->locationService = $locationService;
    }

    public function onMenuConfigure(ConfigureMenuEvent $event): void
    {
        $menu = $event->getMenu();

        $adminMenu = $menu->getChild(MainMenuBuilder::ITEM_ADMIN);
        if (null === $adminMenu) {
            return;
        }

        $dashboardLocationRemoteId = $this->configResolver->getParameter('dashboard.container_remote_id');

        $location = $this->locationService->loadLocationByRemoteId($dashboardLocationRemoteId);

        $dashboardsLocationMenuItem = $this->menuItemFactory->createLocationMenuItem(
            self::ITEM_ADMIN__DASHBOARD,
            $location->id,
            [
                'extras' => [
                    'orderNumber' => 20,
                ],
            ]
        );

        if (null !== $dashboardsLocationMenuItem) {
            $adminMenu->addChild($dashboardsLocationMenuItem);
        }

        $dashboardContentTypeMenuItem = $this->menuItemFactory->createItem(
            self::ITEM_ADMIN__DASHBOARD_CONTENT_TYPE,
            [
                'route' => 'ibexa.dashboard.content_type',
                'extras' => [
                    'orderNumber' => 20,
                ],
            ],
        );

        $adminMenu->addChild($dashboardContentTypeMenuItem);
    }

    /**
     * {@inheritdoc}
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM_ADMIN__DASHBOARD, 'ibexa_menu'))->setDesc('Dashboards'),
            (new Message(self::ITEM_ADMIN__DASHBOARD_CONTENT_TYPE, 'ibexa_menu'))
                ->setDesc('Dashboard type'),
        ];
    }
}
