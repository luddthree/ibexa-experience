<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Event\Listener;

use Ibexa\AdminUi\Menu\Event\ConfigureMenuEvent;
use Ibexa\AdminUi\Menu\MenuItemFactory;
use Ibexa\Personalization\Exception\CustomerIdNotFoundException;
use Ibexa\Personalization\Permission\PermissionCheckerInterface;
use Ibexa\Personalization\Security\Service\SecurityServiceInterface;
use Ibexa\Personalization\Service\Setting\SettingServiceInterface;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\Util\MenuManipulator;

final class ConfigurePersonalizationMenuListener implements TranslationContainerInterface
{
    private const TRANSLATION_DOMAIN = 'ibexa_menu';
    private const ITEM_PERSONALIZATION = 'main__personalization';
    private const ITEM_PERSONALIZATION__GROUP_SETTINGS = 'main__personalization__group_settings';
    private const ITEM_PERSONALIZATION__DASHBOARD = 'main__personalization__dashboard';
    private const ITEM_PERSONALIZATION__MODELS = 'main__personalization__models';
    private const ITEM_PERSONALIZATION__SCENARIOS = 'main__personalization__scenarios';
    private const ITEM_PERSONALIZATION__IMPORT = 'main__personalization__import';

    private MenuItemFactory $menuItemFactory;

    private SecurityServiceInterface $securityService;

    private PermissionCheckerInterface $permissionChecker;

    private SettingServiceInterface $settingService;

    public function __construct(
        MenuItemFactory $menuItemFactory,
        SecurityServiceInterface $securityService,
        PermissionCheckerInterface $permissionChecker,
        SettingServiceInterface $settingService
    ) {
        $this->menuItemFactory = $menuItemFactory;
        $this->securityService = $securityService;
        $this->permissionChecker = $permissionChecker;
        $this->settingService = $settingService;
    }

    /**
     * @param \Ibexa\AdminUi\Menu\Event\ConfigureMenuEvent $event
     */
    public function renderMenu(ConfigureMenuEvent $event): void
    {
        $menu = $event->getMenu();
        $manipulator = new MenuManipulator();

        $this->configurePersonalizationMenu($menu, $manipulator);
    }

    private function configurePersonalizationMenu(ItemInterface $menu, MenuManipulator $menuManipulator): void
    {
        if ($this->permissionChecker->canView($this->getCustomerId())) {
            $personalizationMenu = $this->menuItemFactory
                ->createItem(self::ITEM_PERSONALIZATION, [
                    'attributes' => [
                        'data-tooltip-placement' => 'right',
                        'data-tooltip-extra-class' => 'ibexa-tooltip--navigation',
                    ],
                    'extras' => [
                        'icon' => 'personalize',
                        'orderNumber' => 120,
                    ],
                ])
                ->setChildren(
                    $this->getPersonalizationMenuItems(
                        $this->getCustomerId()
                    )
                );
            $menu->addChild($personalizationMenu);

            $menuManipulator->moveToLastPosition($personalizationMenu);
        }
    }

    /**
     * @return \Knp\Menu\ItemInterface[]
     */
    private function getPersonalizationMenuItems(int $customerId): array
    {
        $settingsRoot = $this->menuItemFactory->createItem(
            self::ITEM_PERSONALIZATION__GROUP_SETTINGS,
        );

        $settingsRoot->addChild(
            self::ITEM_PERSONALIZATION__MODELS,
            [
                'route' => 'ibexa.personalization.models',
                'routeParameters' => [
                    'customerId' => $customerId,
                ],
                'extras' => [
                    'routes' => [
                        'edit' => 'ibexa.personalization.model.edit',
                        'details' => 'ibexa.personalization.model.details',
                    ],
                ],
            ]
        );

        $settingsRoot->addChild(
            self::ITEM_PERSONALIZATION__SCENARIOS,
            [
                'route' => 'ibexa.personalization.scenarios',
                'routeParameters' => [
                    'customerId' => $customerId,
                ],
                'extras' => [
                    'routes' => [
                        'create' => 'ibexa.personalization.scenario.create',
                        'edit' => 'ibexa.personalization.scenario.edit',
                        'details' => 'ibexa.personalization.scenario.details',
                        'preview' => 'ibexa.personalization.scenario.preview',
                    ],
                ],
            ]
        );

        return [
            self::ITEM_PERSONALIZATION__DASHBOARD => $this->menuItemFactory->createItem(
                self::ITEM_PERSONALIZATION__DASHBOARD,
                [
                    'route' => 'ibexa.personalization.dashboard',
                    'routeParameters' => [
                        'customerId' => $customerId,
                    ],
                ]
            ),
            self::ITEM_PERSONALIZATION__IMPORT => $this->menuItemFactory->createItem(
                self::ITEM_PERSONALIZATION__IMPORT,
                [
                    'route' => 'ibexa.personalization.import',
                    'routeParameters' => [
                        'customerId' => $customerId,
                    ],
                ]
            ),
            self::ITEM_PERSONALIZATION__GROUP_SETTINGS => $settingsRoot,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM_PERSONALIZATION, self::TRANSLATION_DOMAIN))->setDesc('Personalization'),
            (new Message(self::ITEM_PERSONALIZATION__DASHBOARD, self::TRANSLATION_DOMAIN))->setDesc('Dashboard'),
            (new Message(self::ITEM_PERSONALIZATION__MODELS, self::TRANSLATION_DOMAIN))->setDesc('Models'),
            (new Message(self::ITEM_PERSONALIZATION__SCENARIOS, self::TRANSLATION_DOMAIN))->setDesc('Scenarios'),
            (new Message(self::ITEM_PERSONALIZATION__IMPORT, self::TRANSLATION_DOMAIN))->setDesc('Import'),
            (new Message(self::ITEM_PERSONALIZATION__GROUP_SETTINGS, self::TRANSLATION_DOMAIN))->setDesc('Settings'),
        ];
    }

    private function getCustomerId(): int
    {
        $customerIdFromRequest = $this->settingService->getCustomerIdFromRequest();

        if (null !== $customerIdFromRequest) {
            return $customerIdFromRequest;
        }

        if (!$this->securityService->hasGrantedAccess()) {
            return 0;
        }

        $firstConfiguredCustomerId = $this->securityService->getCurrentCustomerId();

        /** Returns first configured customer id with granted access */
        if (null !== $firstConfiguredCustomerId) {
            return $firstConfiguredCustomerId;
        }

        throw new CustomerIdNotFoundException();
    }
}

class_alias(ConfigurePersonalizationMenuListener::class, 'Ibexa\Platform\Personalization\Event\Listener\ConfigurePersonalizationMenuListener');
