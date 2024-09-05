<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\EventSubscriber;

use Ibexa\AdminUi\Menu\Event\ConfigureMenuEvent;
use Ibexa\Bundle\Commerce\Eshop\Api\Configuration\CommerceSiteConfig;
use Ibexa\Bundle\ProductCatalog\EventSubscriber\MainMenuSubscriber;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\CustomerGroup\View as ViewCustomerGroup;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\PolicyInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Product\View as ViewProduct;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\ProductType\View as ViewProductType;
use Ibexa\Contracts\ProductCatalog\PermissionResolverInterface;
use Ibexa\Contracts\Rest\Exceptions\NotFoundException;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\ProductCatalog\Config\ConfigProviderInterface;
use Knp\Menu\FactoryInterface as MenuFactoryInterface;
use Knp\Menu\MenuItem;
use PHPUnit\Framework\MockObject\Rule\InvocationOrder;
use PHPUnit\Framework\TestCase;

final class MainMenuSubscriberTest extends TestCase
{
    private MenuFactoryInterface $menuFactory;

    private MainMenuSubscriber $subscriber;

    /** @var \Ibexa\Contracts\ProductCatalog\PermissionResolverInterface&\PHPUnit\Framework\MockObject\MockObject */
    private PermissionResolverInterface $permissionResolver;

    /** @var \Ibexa\ProductCatalog\Config\ConfigProviderInterface&\PHPUnit\Framework\MockObject\MockObject */
    private ConfigProviderInterface $configProvider;

    /** @var \Ibexa\Bundle\Commerce\Eshop\Api\Configuration\CommerceSiteConfig&\PHPUnit\Framework\MockObject\MockObject */
    private CommerceSiteConfig $commerceSiteConfig;

    /** @var \Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface&\PHPUnit\Framework\MockObject\MockObject */
    private TaxonomyServiceInterface $taxonomyService;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService&\PHPUnit\Framework\MockObject\MockObject */
    private ContentService $contentService;

    protected function setUp(): void
    {
        $this->menuFactory = $this->createMock(MenuFactoryInterface::class);
        $this->menuFactory
            ->method('createItem')
            ->willReturnCallback(function (string $name): MenuItem {
                return new MenuItem($name, $this->menuFactory);
            });

        $this->permissionResolver = $this->createMock(PermissionResolverInterface::class);
        $this->configProvider = $this->createMock(ConfigProviderInterface::class);
        $this->commerceSiteConfig = $this->createMock(CommerceSiteConfig::class);
        $this->taxonomyService = $this->createMock(TaxonomyServiceInterface::class);
        $this->contentService = $this->createMock(ContentService::class);

        $this->subscriber = new MainMenuSubscriber(
            $this->permissionResolver,
            $this->configProvider,
            $this->commerceSiteConfig,
            $this->taxonomyService,
            $this->contentService,
            'product_categories',
        );
    }

    /**
     * @dataProvider provideForTest
     *
     * @param array{
     *     user_permissions?: bool|callable(\Ibexa\Contracts\ProductCatalog\Permission\Policy\PolicyInterface): bool,
     *     expect_permission_resolver_to_be_called?: \PHPUnit\Framework\MockObject\Rule\InvocationOrder|null,
     *     is_commerce_enabled?: bool,
     *     engine_alias?: string|null,
     *     taxonomy_root_entry?: \Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry|null,
     * } $options
     * @param array<string, array<MainMenuSubscriber::ITEM_*>> $expectedPaths
     * @param array<string, array<MainMenuSubscriber::ITEM_*>> $expectedNotExistingPaths
     */
    public function testOnConfigureMainMenu(
        array $options,
        array $expectedPaths,
        array $expectedNotExistingPaths = []
    ): void {
        $this->configurePermissionResolver(
            $options['user_permissions'] ?? true,
            $options['expect_permission_resolver_to_be_called'] ?? null,
        );

        $this->commerceSiteConfig
            ->method('isCommerceEnabled')
            ->willReturn($options['is_commerce_enabled'] ?? true);

        $this->configProvider
            ->method('getEngineAlias')
            ->willReturn($this->getEngineAlias($options));

        $this->configProvider
            ->method('getEngineType')
            ->willReturn($this->getEngineType($options));

        if ($this->getEngineType($options) === 'local') {
            $this->configureTaxonomyService($options['taxonomy_root_entry'] ?? null);
        }

        $mainMenu = new MenuItem('root', $this->menuFactory);
        $this->subscriber->onConfigureMainMenu(
            new ConfigureMenuEvent(
                $this->menuFactory,
                $mainMenu,
            )
        );

        foreach ($expectedPaths as $key => $expectedPath) {
            $this->assertMenuContainsPath($key, $expectedPath, $mainMenu);
        }

        foreach ($expectedNotExistingPaths as $key => $expectedNotExistingPath) {
            $this->assertMenuNotContainsPath($key, $expectedNotExistingPath, $mainMenu);
        }
    }

    /**
     * @phpstan-return iterable<string, array{
     *     array{
     *         user_permissions?: bool|callable(\Ibexa\Contracts\ProductCatalog\Permission\Policy\PolicyInterface): bool,
     *         expect_permission_resolver_to_be_called?: \PHPUnit\Framework\MockObject\Rule\InvocationOrder|null,
     *         is_commerce_enabled?: bool,
     *         engine_alias?: string|null,
     *         taxonomy_root_entry?: \Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry|null,
     *     },
     *     array<string, array<MainMenuSubscriber::ITEM_*>>,
     *     2?: array<string, array<MainMenuSubscriber::ITEM_*>>,
     * }>
     */
    public function provideForTest(): iterable
    {
        yield 'Product engine is not configured' => [
            [
                'engine_alias' => null,
                'expect_permission_resolver_to_be_called' => self::never(),
            ],
            [],
            [
                'Main Menu / Product catalog' => [
                    MainMenuSubscriber::ITEM_PRODUCT_CATALOG,
                ],
            ],
        ];

        $productsListRoute = [
            MainMenuSubscriber::ITEM_PRODUCT_CATALOG,
            MainMenuSubscriber::ITEM_PRODUCT_CATALOG_PRODUCTS,
        ];

        $productCatalogRoute = [
            MainMenuSubscriber::ITEM_PRODUCT_CATALOG,
            MainMenuSubscriber::ITEM_PRODUCT_CATALOG_CATALOGS,
        ];

        $groupSettingsProductTypesRoute = [
            MainMenuSubscriber::ITEM_PRODUCT_CATALOG,
            MainMenuSubscriber::ITEM_PRODUCT_GROUP_SETTINGS,
            MainMenuSubscriber::ITEM_PRODUCT_CATALOG_PRODUCT_TYPES,
        ];

        $groupSettingsAttributeGroupsListRoute = [
            MainMenuSubscriber::ITEM_PRODUCT_CATALOG,
            MainMenuSubscriber::ITEM_PRODUCT_GROUP_SETTINGS,
            MainMenuSubscriber::ITEM_PRODUCT_CATALOG_ATTRIBUTE_GROUPS,
        ];

        $groupSettingsAttributesListRoute = [
            MainMenuSubscriber::ITEM_PRODUCT_CATALOG,
            MainMenuSubscriber::ITEM_PRODUCT_GROUP_SETTINGS,
            MainMenuSubscriber::ITEM_PRODUCT_CATALOG_ATTRIBUTES,
        ];

        $commerceCustomerGroupsListRoute = [
            MainMenuSubscriber::ITEM_COMMERCE,
            MainMenuSubscriber::ITEM_PRODUCT_CATALOG_CUSTOMER_GROUPS,
        ];

        yield 'Basic setup' => [
            [],
            [
                'Main Menu / Product catalog / Products' => $productsListRoute,
                'Main Menu / Product catalog / Catalogs' => $productCatalogRoute,
                'Main Menu / Product catalog / Settings / Product Types' => $groupSettingsProductTypesRoute,
                'Main Menu / Product catalog / Settings / AttributeGroups' => $groupSettingsAttributeGroupsListRoute,
                'Main Menu / Product catalog / Settings / Attributes' => $groupSettingsAttributesListRoute,
                'Main Menu / eCommerce / Customer Groups' => $commerceCustomerGroupsListRoute,
            ],
        ];

        yield 'without ViewProduct permissions' => [
            [
                'user_permissions' => static fn (PolicyInterface $policy): bool => !$policy instanceof ViewProduct,
            ],
            [
                'Main Menu / eCommerce / Customer Groups' => $commerceCustomerGroupsListRoute,
            ],
            [
                'Main Menu / Product catalog / Products' => $productsListRoute,
                'Main Menu / Product catalog / Catalogs' => $productCatalogRoute,
            ],
        ];

        yield 'without ViewCustomerGroup permissions' => [
            [
                'user_permissions' => static fn (PolicyInterface $policy): bool => !$policy instanceof ViewCustomerGroup,
            ],
            [
                'Main Menu / Product catalog / Products' => $productsListRoute,
                'Main Menu / Product catalog / Catalogs' => $productCatalogRoute,
            ],
            [
                'Main Menu / eCommerce / Customer Groups' => $commerceCustomerGroupsListRoute,
            ],
        ];

        $productTypesListRoute = [
            MainMenuSubscriber::ITEM_PRODUCT_CATALOG,
            MainMenuSubscriber::ITEM_PRODUCT_CATALOG_PRODUCT_TYPES,
        ];

        $attributeGroupsListRoute = [
            MainMenuSubscriber::ITEM_PRODUCT_CATALOG,
            MainMenuSubscriber::ITEM_PRODUCT_CATALOG_ATTRIBUTE_GROUPS,
        ];

        $attributesListRoute = [
            MainMenuSubscriber::ITEM_PRODUCT_CATALOG,
            MainMenuSubscriber::ITEM_PRODUCT_CATALOG_ATTRIBUTES,
        ];

        yield 'without ViewProductType permissions' => [
            [
                'user_permissions' => static fn (PolicyInterface $policy): bool => !$policy instanceof ViewProductType,
            ],
            [
                'Main Menu / Product catalog / Products' => $productsListRoute,
                'Main Menu / Product catalog / Catalogs' => $productCatalogRoute,
                'Main Menu / eCommerce / Customer Groups' => $commerceCustomerGroupsListRoute,
            ],
            [
                'Main Menu / Product catalog / Settings / Product Types' => $productTypesListRoute,
                'Main Menu / Product catalog / Settings / Attribute Groups' => $attributeGroupsListRoute,
                'Main Menu / Product catalog / Settings / Attributes' => $attributesListRoute,
            ],
        ];

        $customerGroupsListRoute = [
            MainMenuSubscriber::ITEM_PRODUCT_CATALOG,
            MainMenuSubscriber::ITEM_PRODUCT_CATALOG_CUSTOMER_GROUPS,
        ];

        $commerceRoute = [
            MainMenuSubscriber::ITEM_COMMERCE,
        ];

        yield 'without ViewProductType permissions + disabled commerce' => [
            [
                'user_permissions' => static fn (PolicyInterface $policy): bool => !$policy instanceof ViewProductType,
                'is_commerce_enabled' => false,
            ],
            [
                'Main Menu / Product catalog / Products' => $productsListRoute,
                'Main Menu / Product catalog / Customer Groups' => $customerGroupsListRoute,
            ],
            [
                'Main Menu / eCommerce' => $commerceRoute,
                'Main Menu / Product catalog / Settings / Product Types' => $productTypesListRoute,
                'Main Menu / Product catalog / Settings / Attribute Groups' => $attributeGroupsListRoute,
                'Main Menu / Product catalog / Settings / Attributes' => $attributesListRoute,
            ],
        ];

        $taxonomyEntry = $this->createMock(TaxonomyEntry::class);
        $content = $this->createMock(Content::class);
        $content->method('__get')
            ->with(self::identicalTo('id'))
            ->willReturn(1);

        $taxonomyEntry->method('__get')
            ->with(self::identicalTo('content'))
            ->willReturn($content);

        yield 'With taxonomy and local PIM' => [
            [
                'taxonomy_root_entry' => $taxonomyEntry,
            ],
            [
                'Main Menu / Product catalog / Categories' => [
                    MainMenuSubscriber::ITEM_PRODUCT_CATALOG,
                    MainMenuSubscriber::ITEM_PRODUCT_CATALOG_CATEGORIES,
                ],
            ],
        ];

        yield 'With taxonomy and remote PIM' => [
            [
                'taxonomy_root_entry' => $taxonomyEntry,
                'engine_type' => 'remote',
            ],
            [],
            [
                'Main Menu / Product catalog / Categories' => [
                    MainMenuSubscriber::ITEM_PRODUCT_CATALOG,
                    MainMenuSubscriber::ITEM_PRODUCT_CATALOG_CATEGORIES,
                ],
            ],
        ];
    }

    /**
     * @param string[] $path
     */
    private function assertMenuContainsPath(string $key, array $path, MenuItem $menu): void
    {
        $item = $menu;
        $checkedPathItems = [];
        foreach ($path as $name) {
            $item = $item->getChild($name);
            $checkedPathItems[] = $name;
            $currentPath = implode('.', $checkedPathItems);

            self::assertNotNull(
                $item,
                sprintf(
                    "Unable to reach '%s' element (%s). Expected path '%s' to exist",
                    $name,
                    $key,
                    $currentPath,
                ),
            );
        }
    }

    /**
     * @param string[] $path
     */
    private function assertMenuNotContainsPath(string $key, array $path, MenuItem $item): void
    {
        $last = array_pop($path) ?? '';
        $checkedPathItems = [];
        foreach ($path as $name) {
            $item = $item->getChild($name);
            $checkedPathItems[] = $name;
            $currentPath = implode('.', $checkedPathItems);

            self::assertNotNull(
                $item,
                sprintf(
                    "Unable to reach '%s' element (%s). Expected path '%s' to exist",
                    $last,
                    $key,
                    $currentPath,
                ),
            );
        }

        $lastItem = $item->getChild($last);

        self::assertNull(
            $lastItem,
            sprintf(
                "Given menu does contain '%s' (%s) item at path: '%s'",
                $last,
                $key,
                implode('.', $checkedPathItems),
            ),
        );
    }

    /**
     * @param bool|callable(\Ibexa\Contracts\ProductCatalog\Permission\Policy\PolicyInterface): bool $permissionResolverConfiguration
     */
    private function configurePermissionResolver(
        $permissionResolverConfiguration,
        ?InvocationOrder $invocationOrder = null
    ): void {
        $invocationMock = $this->permissionResolver
            ->expects($invocationOrder ?? self::atLeastOnce())
            ->method('canUser');

        if (is_bool($permissionResolverConfiguration)) {
            $invocationMock->willReturn($permissionResolverConfiguration);

            return;
        }

        $invocationMock->willReturnCallback($permissionResolverConfiguration);
    }

    private function configureTaxonomyService(?TaxonomyEntry $entry): void
    {
        if ($entry === null) {
            $this->taxonomyService
                ->method('loadRootEntry')
                ->willThrowException($this->createMock(NotFoundException::class));

            $this->contentService
                ->expects(self::never())
                ->method('loadContentInfo');

            return;
        }

        $this->taxonomyService
            ->expects(self::once())
            ->method('loadRootEntry')
            ->willReturn($entry);
    }

    /**
     * @param array{
     *     user_permissions?: bool|callable(\Ibexa\Contracts\ProductCatalog\Permission\Policy\PolicyInterface): bool,
     *     expect_permission_resolver_to_be_called?: \PHPUnit\Framework\MockObject\Rule\InvocationOrder|null,
     *     is_commerce_enabled?: bool,
     *     engine_alias?: string|null,
     *     taxonomy_root_entry?: \Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry|null,
     *  } $options
     */
    private function getEngineAlias(array $options): ?string
    {
        return array_key_exists('engine_alias', $options) ? $options['engine_alias'] : 'local';
    }

    /**
     * @param array{
     *     user_permissions?: bool|callable(\Ibexa\Contracts\ProductCatalog\Permission\Policy\PolicyInterface): bool,
     *     expect_permission_resolver_to_be_called?: \PHPUnit\Framework\MockObject\Rule\InvocationOrder|null,
     *     is_commerce_enabled?: bool,
     *     engine_alias?: string|null,
     *     engine_type?: string|null,
     *     taxonomy_root_entry?: \Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry|null,
     *  } $options
     */
    private function getEngineType(array $options): ?string
    {
        return array_key_exists('engine_type', $options) ? $options['engine_type'] : 'local';
    }
}
