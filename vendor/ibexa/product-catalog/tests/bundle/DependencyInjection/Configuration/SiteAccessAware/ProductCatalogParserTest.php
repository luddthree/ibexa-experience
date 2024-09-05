<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\DependencyInjection\Configuration\SiteAccessAware;

use Exception;
use Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension;
use Ibexa\Bundle\ProductCatalog\DependencyInjection\Configuration\SiteAccessAware\ProductCatalogParser;
use Ibexa\Core\MVC\Exception\ParameterNotFoundException;
use Ibexa\Tests\Bundle\Core\DependencyInjection\Configuration\Parser\AbstractParserTestCase;

/**
 * @covers \Ibexa\Bundle\ProductCatalog\DependencyInjection\Configuration\SiteAccessAware\ProductCatalogParser
 */
final class ProductCatalogParserTest extends AbstractParserTestCase
{
    protected function getContainerExtensions(): array
    {
        return [
            new IbexaCoreExtension([
                new ProductCatalogParser(),
            ]),
        ];
    }

    /**
     * @dataProvider dataProviderForTestSettings
     *
     * @param array<string,mixed> $config
     * @param array<string,mixed> $expected
     * @param array<string> $expectedNotSet
     */
    public function testSettings(array $config, array $expected, array $expectedNotSet = []): void
    {
        $this->load([
            'system' => [
                'ibexa_demo_site' => $config,
            ],
        ]);

        foreach ($expected as $key => $val) {
            $this->assertConfigResolverParameterValue($key, $val, 'ibexa_demo_site');
        }

        foreach ($expectedNotSet as $key) {
            $this->assertConfigResolverParameterIsNotSet($key, 'ibexa_demo_site');
        }
    }

    /**
     * @phpstan-return iterable<
     *     string,
     *     array{
     *         array<string, mixed>,
     *         array<string, mixed>,
     *         2?: array<string>,
     *     },
     * >
     */
    public function dataProviderForTestSettings(): iterable
    {
        yield 'empty configuration' => [
            [],
            [],
            [
                'product_catalog.pagination.attribute_definitions_limit',
                'product_catalog.pagination.attribute_groups_limit',
                'product_catalog.pagination.products_limit',
                'product_catalog.pagination.product_types_limit',
                'product_catalog.pagination.currencies_limit',
                'product_catalog.pagination.customer_groups_limit',
                'product_catalog.pagination.customer_group_users_limit',
                'product_catalog.pagination.regions_limit',
                'product_catalog.pagination.product_view_custom_prices_limit',
                'product_catalog.pagination.catalogs_limit',
                'product_catalog.regions',
                'product_catalog.currencies',
                'product_catalog.catalogs',
            ],
        ];

        yield 'pagination' => [
            [
                'product_catalog' => [
                    'pagination' => [
                        'attribute_definitions_limit' => 10,
                        'attribute_groups_limit' => 10,
                        'customer_groups_limit' => 10,
                        'customer_group_users_limit' => 10,
                        'currencies_limit' => 10,
                        'products_limit' => 10,
                        'product_types_limit' => 10,
                        'product_view_custom_prices_limit' => 10,
                        'regions_limit' => 10,
                        'catalogs_limit' => 10,
                    ],
                ],
            ],
            [
                'product_catalog.pagination.attribute_definitions_limit' => 10,
                'product_catalog.pagination.attribute_groups_limit' => 10,
                'product_catalog.pagination.customer_groups_limit' => 10,
                'product_catalog.pagination.currencies_limit' => 10,
                'product_catalog.pagination.customer_group_users_limit' => 10,
                'product_catalog.pagination.products_limit' => 10,
                'product_catalog.pagination.product_types_limit' => 10,
                'product_catalog.pagination.product_view_custom_prices_limit' => 10,
                'product_catalog.pagination.regions_limit' => 10,
                'product_catalog.pagination.catalogs_limit' => 10,
            ],
            [
                'product_catalog.regions',
                'product_catalog.catalogs',
            ],
        ];

        yield 'filter_preview_templates' => [
            [
                'product_catalog' => [
                    'catalogs' => [
                        'filter_preview_templates' => [
                            [
                                'template' => '@ibexadesign/product_catalog/catalog/edit/filter_preview_blocks.html.twig',
                                'priority' => 10,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'product_catalog.filter_preview_templates' => [
                    [
                        'template' => '@ibexadesign/product_catalog/catalog/edit/filter_preview_blocks.html.twig',
                        'priority' => 10,
                    ],
                ],
            ],
        ];

        yield 'default_filters' => [
            [
                'product_catalog' => [
                    'catalogs' => [
                        'default_filters' => [
                            'foo',
                            'bar',
                        ],
                    ],
                ],
            ],
            [
                'product_catalog.default_filters' => [
                    'foo',
                    'bar',
                ],
            ],
        ];

        yield 'regions' => [
            [
                'product_catalog' => [
                    'regions' => [
                        'A DIFFERENT REGION',
                        'BE-be',
                    ],
                ],
            ],
            [
                'product_catalog.regions' => [
                    'A DIFFERENT REGION',
                    'BE-be',
                ],
            ],
        ];

        yield 'currencies' => [
            [
                'product_catalog' => [
                    'currencies' => [
                        1,
                        'FOO',
                    ],
                ],
            ],
            [
                'product_catalog.currencies' => [
                    '1',
                    'FOO',
                ],
            ],
        ];
    }

    private function assertConfigResolverParameterIsNotSet(string $parameterName, ?string $scope = null): void
    {
        $chainConfigResolver = $this->getConfigResolver();
        try {
            $chainConfigResolver->getParameter($parameterName, 'ibexa.site_access.config', $scope);
            self::fail(sprintf('Parameter "%s" should not exist in scope "%s"', $parameterName, $scope));
        } catch (Exception $e) {
            self::assertInstanceOf(ParameterNotFoundException::class, $e);
        }
    }
}
