<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\EventSubscriber;

use Ibexa\AdminUi\Menu\Event\ConfigureMenuEvent;
use Ibexa\Bundle\Commerce\Eshop\Api\Configuration\CommerceSiteConfig;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Commerce\AdministrateCurrencies;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\CustomerGroup\View as ViewCustomerGroup;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Product\View as ViewProduct;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\ProductType\View as ViewProductType;
use Ibexa\Contracts\ProductCatalog\PermissionResolverInterface;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\ProductCatalog\Config\ConfigProviderInterface;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Translation\TranslationContainerInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class MainMenuSubscriber implements EventSubscriberInterface, TranslationContainerInterface
{
    // Main Menu / Product catalog
    public const ITEM_PRODUCT_CATALOG = 'main__product_catalog';
    // Main Menu / Product catalog / Products
    public const ITEM_PRODUCT_CATALOG_PRODUCTS = 'main__product_catalog__products';
    // Main Menu / Product catalog / Catalogs
    public const ITEM_PRODUCT_CATALOG_CATALOGS = 'main__product_catalog__catalogs';
    // Main Menu / Product catalog / Product Categories
    public const ITEM_PRODUCT_CATALOG_CATEGORIES = 'main__product_catalog__categories';
    // Main Menu / Product catalog / Settings
    public const ITEM_PRODUCT_GROUP_SETTINGS = 'main__product_catalog__group_settings';
    // Main Menu / Product catalog / Settings / Product Types
    public const ITEM_PRODUCT_CATALOG_PRODUCT_TYPES = 'main__product_catalog__product_types';
    // Main Menu / Product catalog / Settings / Attribute Groups
    public const ITEM_PRODUCT_CATALOG_ATTRIBUTE_GROUPS = 'main__product_catalog__attribute_groups';
    // Main Menu / Product catalog / Settings / Attributes
    public const ITEM_PRODUCT_CATALOG_ATTRIBUTES = 'main__product_catalog__attributes';
    // Main Menu / Product catalog / Customer Groups
    public const ITEM_PRODUCT_CATALOG_CUSTOMER_GROUPS = 'main__product_catalog__customer_groups';
    // Main Menu / Product catalog / Currencies
    public const ITEM_PRODUCT_CATALOG_CURRENCIES = 'main__product_catalog__currencies';
    // Main Menu / eCommerce
    public const ITEM_COMMERCE = 'main__commerce';

    private PermissionResolverInterface $permissionResolver;

    private ConfigProviderInterface $configProvider;

    private ?CommerceSiteConfig $commerceSiteConfig;

    private TaxonomyServiceInterface $taxonomyService;

    private ContentService $contentService;

    private string $productTaxonomyName;

    public function __construct(
        PermissionResolverInterface $permissionResolver,
        ConfigProviderInterface $configProvider,
        ?CommerceSiteConfig $commerceSiteConfig,
        TaxonomyServiceInterface $taxonomyService,
        ContentService $contentService,
        string $productTaxonomyName
    ) {
        $this->permissionResolver = $permissionResolver;
        $this->configProvider = $configProvider;
        $this->commerceSiteConfig = $commerceSiteConfig;
        $this->taxonomyService = $taxonomyService;
        $this->contentService = $contentService;
        $this->productTaxonomyName = $productTaxonomyName;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ConfigureMenuEvent::MAIN_MENU => ['onConfigureMainMenu', 15],
        ];
    }

    public function onConfigureMainMenu(ConfigureMenuEvent $event): void
    {
        $menu = $event->getMenu();
        $this->addCommerceMenu($menu);

        if ($this->configProvider->getEngineAlias() === null) {
            return;
        }

        $productCatalogRoot = $menu->addChild(
            self::ITEM_PRODUCT_CATALOG,
            [
                'attributes' => [
                    'data-tooltip-placement' => 'right',
                    'data-tooltip-extra-class' => 'ibexa-tooltip--navigation',
                ],
                'extras' => [
                    'icon' => 'product-catalog',
                    'orderNumber' => 80,
                    'routes' => [
                        [
                            'pattern' => $this->getProductCatalogRoutePattern(),
                        ],
                    ],
                ],
            ]
        );

        if ($this->canViewProducts()) {
            $productCatalogRoot->addChild(
                self::ITEM_PRODUCT_CATALOG_PRODUCTS,
                [
                    'route' => 'ibexa.product_catalog.product.list',
                    'extras' => [
                        'routes' => [
                            [
                                'pattern' => '~^ibexa\.product_catalog\.product\.~',
                            ],
                        ],
                        'orderNumber' => 10,
                    ],
                ]
            );
            $productCatalogRoot->addChild(
                self::ITEM_PRODUCT_CATALOG_CATALOGS,
                [
                    'route' => 'ibexa.product_catalog.catalog.list',
                    'extras' => [
                        'routes' => [
                            [
                                'pattern' => '~^ibexa\.product_catalog\.catalog\.~',
                            ],
                        ],
                        'orderNumber' => 20,
                    ],
                ]
            );

            if ($this->configProvider->getEngineType() === 'local') {
                try {
                    $entry = $this->taxonomyService->loadRootEntry($this->productTaxonomyName);
                    $rootContent = $this->contentService->loadContentInfo($entry->content->id);

                    $productCatalogRoot->addChild(
                        self::ITEM_PRODUCT_CATALOG_CATEGORIES,
                        [
                            'route' => 'ibexa.content.view',
                            'routeParameters' => [
                                'contentId' => $rootContent->id,
                                'locationId' => $rootContent->mainLocationId,
                            ],
                            'extras' => [
                                'translation_domain' => 'ibexa_menu',
                                'orderNumber' => 25,
                                'taxonomy' => $this->productTaxonomyName,
                            ],
                        ]
                    );
                } catch (UnauthorizedException|NotFoundException $exception) {
                    // ignore
                }
            }
        }

        if ($this->canViewProductTypes()) {
            $groupSettingsRoot = $productCatalogRoot->addChild(
                self::ITEM_PRODUCT_GROUP_SETTINGS,
                [
                    'extras' => [
                        'orderNumber' => 30,
                    ],
                ],
            );

            $groupSettingsRoot->addChild(
                self::ITEM_PRODUCT_CATALOG_PRODUCT_TYPES,
                [
                    'route' => 'ibexa.product_catalog.product_type.list',
                    'extras' => [
                        'routes' => [
                            [
                                'pattern' => '~^ibexa\.product_catalog\.product_type\.~',
                            ],
                        ],
                        'orderNumber' => 40,
                    ],
                ]
            );

            $groupSettingsRoot->addChild(
                self::ITEM_PRODUCT_CATALOG_ATTRIBUTE_GROUPS,
                [
                    'route' => 'ibexa.product_catalog.attribute_group.list',
                    'extras' => [
                        'routes' => [
                            [
                                'pattern' => '~^ibexa\.product_catalog\.attribute_group\.~',
                            ],
                        ],
                        'orderNumber' => 20,
                    ],
                ]
            );

            $groupSettingsRoot->addChild(
                self::ITEM_PRODUCT_CATALOG_ATTRIBUTES,
                [
                    'route' => 'ibexa.product_catalog.attribute_definition.list',
                    'extras' => [
                        'routes' => [
                            [
                                'pattern' => '~^ibexa\.product_catalog\.attribute_definition\.~',
                            ],
                        ],
                        'orderNumber' => 30,
                    ],
                ]
            );
        }

        $rootElement = $menu->getChild(self::ITEM_COMMERCE) ?? $productCatalogRoot;

        if ($this->canViewCustomerGroups()) {
            $rootElement->addChild(
                self::ITEM_PRODUCT_CATALOG_CUSTOMER_GROUPS,
                [
                    'route' => 'ibexa.product_catalog.customer_group.list',
                    'extras' => [
                        'routes' => [
                            [
                                'pattern' => '~^ibexa\.product_catalog\.customer_group\.~',
                            ],
                        ],
                        'orderNumber' => 40,
                    ],
                ]
            );
        }

        if ($this->canAdministrateCurrencies()) {
            $rootElement->addChild(
                self::ITEM_PRODUCT_CATALOG_CURRENCIES,
                [
                    'route' => 'ibexa.product_catalog.currency.list',
                    'extras' => [
                        'routes' => [
                            [
                                'pattern' => '~^ibexa\.product_catalog\.currency\.~',
                            ],
                        ],
                        'orderNumber' => 50,
                    ],
                ]
            );
        }
    }

    public static function getTranslationMessages(): array
    {
        return [
            (new Message(self::ITEM_PRODUCT_CATALOG, 'ibexa_menu'))->setDesc('Product catalog'),
            (new Message(self::ITEM_PRODUCT_CATALOG_PRODUCTS, 'ibexa_menu'))->setDesc('Products'),
            (new Message(self::ITEM_PRODUCT_CATALOG_CATALOGS, 'ibexa_menu'))->setDesc('Catalogs'),
            (new Message(self::ITEM_PRODUCT_GROUP_SETTINGS, 'ibexa_menu'))->setDesc('Settings'),
            (new Message(self::ITEM_PRODUCT_CATALOG_PRODUCT_TYPES, 'ibexa_menu'))->setDesc('Product Types'),
            (new Message(self::ITEM_PRODUCT_CATALOG_ATTRIBUTE_GROUPS, 'ibexa_menu'))->setDesc('Attribute Groups'),
            (new Message(self::ITEM_PRODUCT_CATALOG_ATTRIBUTES, 'ibexa_menu'))->setDesc('Attributes'),
            (new Message(self::ITEM_PRODUCT_CATALOG_CUSTOMER_GROUPS, 'ibexa_menu'))->setDesc('Customer Groups'),
            (new Message(self::ITEM_PRODUCT_CATALOG_CURRENCIES, 'ibexa_menu'))->setDesc('Currencies'),
            (new Message(self::ITEM_COMMERCE, 'ibexa_menu'))->setDesc('Commerce'),
            (new Message(self::ITEM_PRODUCT_CATALOG_CATEGORIES, 'ibexa_menu'))->setDesc('Categories'),
        ];
    }

    private function addCommerceMenu(ItemInterface $menu): void
    {
        if (!$this->isCommerceEnabled()) {
            return;
        }

        $menu->addChild(
            self::ITEM_COMMERCE,
            [
                'attributes' => [
                    'data-tooltip-placement' => 'right',
                    'data-tooltip-extra-class' => 'ibexa-tooltip--navigation',
                ],
                'extras' => [
                    'icon' => 'cart-full',
                    'orderNumber' => 100,
                ],
            ]
        );
    }

    private function canViewProducts(): bool
    {
        return $this->permissionResolver->canUser(new ViewProduct());
    }

    private function canViewCustomerGroups(): bool
    {
        return $this->permissionResolver->canUser(new ViewCustomerGroup());
    }

    private function canViewProductTypes(): bool
    {
        return $this->permissionResolver->canUser(new ViewProductType());
    }

    private function canAdministrateCurrencies(): bool
    {
        return $this->permissionResolver->canUser(new AdministrateCurrencies());
    }

    private function getProductCatalogRoutePattern(): string
    {
        return $this->isCommerceEnabled() ?
            '~^ibexa\.product_catalog\.(?!(customer_group|currency))~' :
            '~^ibexa\.product_catalog\.~';
    }

    private function isCommerceEnabled(): bool
    {
        return $this->commerceSiteConfig !== null && $this->commerceSiteConfig->isCommerceEnabled();
    }
}
