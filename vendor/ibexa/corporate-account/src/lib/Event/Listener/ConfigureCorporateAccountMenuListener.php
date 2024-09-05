<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Event\Listener;

use Ibexa\AdminUi\Menu\Event\ConfigureMenuEvent;
use Ibexa\AdminUi\Menu\MainMenuBuilder;
use Ibexa\AdminUi\Menu\MenuItemFactory;
use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\CorporateAccount\Configuration\CorporateAccountConfiguration;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\Util\MenuManipulator;

final class ConfigureCorporateAccountMenuListener implements TranslationContainerInterface
{
    private const TRANSLATION_DOMAIN = 'ibexa_menu';
    private const ITEM_CORPORATEACCOUNT = 'main__corporateaccount';
    private const ITEM_CORPORATEACCOUNT__COMPANIES = 'main__corporateaccount__companies';
    private const ITEM_CORPORATEACCOUNT__INDIVIDUALS = 'main__corporateaccount__individuals';
    private const ITEM_CORPORATEACCOUNT__APPLICATIONS = 'main__corporateaccount__applications';
    private const ITEM_CORPORATEACCOUNT__SETTINGS = 'main__corporateaccount__settings';
    private const ITEM_CORPORATEACCOUNT__CONTENT_TYPES = 'main__corporateaccount__content_types';
    private const ITEM_CONTENT__CORPORATEACCOUNT = 'main__content__corporateaccount';

    private MenuItemFactory $menuItemFactory;

    private LocationService $locationService;

    private CorporateAccountConfiguration $corporateAccount;

    private ContentTypeService $contentTypeService;

    public function __construct(
        MenuItemFactory $menuItemFactory,
        LocationService $locationService,
        CorporateAccountConfiguration $corporateAccount,
        ContentTypeService $contentTypeService
    ) {
        $this->menuItemFactory = $menuItemFactory;
        $this->locationService = $locationService;
        $this->corporateAccount = $corporateAccount;
        $this->contentTypeService = $contentTypeService;
    }

    /**
     * @param \Ibexa\AdminUi\Menu\Event\ConfigureMenuEvent $event
     */
    public function renderMenu(ConfigureMenuEvent $event): void
    {
        $menu = $event->getMenu();
        $manipulator = new MenuManipulator();

        $this->configureCorporateAccountMenu($menu, $manipulator);
    }

    private function configureCorporateAccountMenu(ItemInterface $menu, MenuManipulator $menuManipulator): void
    {
        $adminMenu = $menu->getChild(MainMenuBuilder::ITEM_ADMIN);

        if (null !== $adminMenu) {
            $rootCorporateRemoteId = $this->corporateAccount->getParentLocationRemoteId();
            $rootLocation = $this->locationService->loadLocationByRemoteId($rootCorporateRemoteId);

            $corporateItem = $this->menuItemFactory->createLocationMenuItem(
                self::ITEM_CONTENT__CORPORATEACCOUNT,
                $rootLocation->id,
                [
                    'label' => self::ITEM_CONTENT__CORPORATEACCOUNT,
                    'extras' => [
                        'orderNumber' => 65,
                    ],
                ]
            );
            if ($corporateItem) {
                $adminMenu->addChild($corporateItem);
            }
        }

        $corporateAccountMenu = $this->menuItemFactory
            ->createItem(self::ITEM_CORPORATEACCOUNT, [
                'attributes' => [
                    'data-tooltip-placement' => 'right',
                    'data-tooltip-extra-class' => 'ibexa-tooltip--navigation',
                ],
                'extras' => [
                    'icon' => 'banner',
                    'orderNumber' => 130,
                ],
            ])
            ->setChildren(
                $this->getCorporateAccountMenuItems()
            );
        $menu->addChild($corporateAccountMenu);

        $menuManipulator->moveToLastPosition($corporateAccountMenu);
    }

    /**
     * @return \Knp\Menu\ItemInterface[]
     */
    private function getCorporateAccountMenuItems(): array
    {
        $settingsItem = $this->menuItemFactory->createItem(
            self::ITEM_CORPORATEACCOUNT__SETTINGS
        );

        $contentTypeGroupIdentifier = $this->corporateAccount->getContentTypeGroupIdentifier();
        $corporateAccountContentTypeGroup = $this->contentTypeService->loadContentTypeGroupByIdentifier($contentTypeGroupIdentifier);
        $corporateAccountContentTypeGroupId = $corporateAccountContentTypeGroup->id;
        $settingsItem->addChild(
            self::ITEM_CORPORATEACCOUNT__CONTENT_TYPES,
            [
                'route' => 'ibexa.content_type_group.view',
                'routeParameters' => [
                    'contentTypeGroupId' => $corporateAccountContentTypeGroupId,
                ],
                'current' => false,
            ]
        );

        return [
            self::ITEM_CORPORATEACCOUNT__COMPANIES => $this->menuItemFactory->createItem(
                self::ITEM_CORPORATEACCOUNT__COMPANIES,
                [
                    'route' => 'ibexa.corporate_account.company.list',
                    'extras' => [
                        'routes' => [
                            [
                                'pattern' => '~^ibexa\.corporate_account\.company\.~',
                            ],
                        ],
                    ],
                ]
            ),
            self::ITEM_CORPORATEACCOUNT__APPLICATIONS => $this->menuItemFactory->createItem(
                self::ITEM_CORPORATEACCOUNT__APPLICATIONS,
                [
                    'route' => 'ibexa.corporate_account.application.list',
                    'extras' => [
                        'routes' => [
                            [
                                'pattern' => '~^ibexa\.corporate_account\.application\.~',
                            ],
                        ],
                    ],
                ]
            ),
            self::ITEM_CORPORATEACCOUNT__INDIVIDUALS => $this->menuItemFactory->createItem(
                self::ITEM_CORPORATEACCOUNT__INDIVIDUALS,
                [
                    'route' => 'ibexa.corporate_account.individual.list',
                    'extras' => [
                        'routes' => [
                            [
                                'pattern' => '~^ibexa\.corporate_account\.individual\.~',
                            ],
                        ],
                    ],
                ]
            ),
            self::ITEM_CORPORATEACCOUNT__SETTINGS => $settingsItem,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM_CORPORATEACCOUNT, self::TRANSLATION_DOMAIN))->setDesc('Customers'),
            (new Message(self::ITEM_CORPORATEACCOUNT__COMPANIES, self::TRANSLATION_DOMAIN))->setDesc('Companies'),
            (new Message(self::ITEM_CORPORATEACCOUNT__INDIVIDUALS, self::TRANSLATION_DOMAIN))->setDesc('Clients'),
            (new Message(self::ITEM_CORPORATEACCOUNT__APPLICATIONS, self::TRANSLATION_DOMAIN))->setDesc('Applications'),
            (new Message(self::ITEM_CORPORATEACCOUNT__SETTINGS, self::TRANSLATION_DOMAIN))->setDesc('Settings'),
            (new Message(self::ITEM_CORPORATEACCOUNT__CONTENT_TYPES, self::TRANSLATION_DOMAIN))->setDesc('B2B types'),
            (new Message(self::ITEM_CONTENT__CORPORATEACCOUNT, self::TRANSLATION_DOMAIN))->setDesc('Corporate'),
        ];
    }
}
