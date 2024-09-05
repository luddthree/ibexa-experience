<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\DependencyInjection\Configuration\RepositoryAware;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\Parser\Repository as RepositoryConfigParser;
use Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension;
use Ibexa\Bundle\ProductCatalog\DependencyInjection\Configuration\RepositoryAware\ProductCatalogParser;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

final class ProductCatalogParserTest extends AbstractExtensionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->container->setParameter('kernel.environment', 'unit');
        $this->container->setParameter('kernel.project_dir', null);
    }

    protected function getContainerExtensions(): array
    {
        return [
            new IbexaCoreExtension([], [
                new RepositoryConfigParser\Storage(),
                new RepositoryConfigParser\Search(),
                new RepositoryConfigParser\FieldGroups(),
                new RepositoryConfigParser\Options(),
                // Configuration parser under test
                new ProductCatalogParser(),
            ]),
        ];
    }

    public function testEmptyConfiguration(): void
    {
        $config = $this->buildConfiguration([]);
        $this->load($config);

        $repositories = $this->container->getParameter('ibexa.repositories');

        self::assertIsArray($repositories);
        self::assertEquals([
            'engine' => null,
            'regions' => [],
        ], $repositories['default']['product_catalog']);
    }

    public function testEngineConfiguration(): void
    {
        $config = $this->buildConfiguration([
            'engine' => 'local',
        ]);
        $this->load($config);

        $repositories = $this->container->getParameter('ibexa.repositories');

        self::assertIsArray($repositories);
        self::assertEquals([
            'engine' => 'local',
            'regions' => [],
        ], $repositories['default']['product_catalog']);
    }

    /**
     * @dataProvider provideDataForTestRegionConfiguration
     *
     * @param array<string, array<mixed>> $expectedVatCategories
     * @param array<string, array<mixed>> $config
     */
    public function testRegionConfiguration(
        array $expectedVatCategories,
        array $config
    ): void {
        $this->load($config);

        $repositories = $this->container->getParameter('ibexa.repositories');

        self::assertIsArray($repositories);
        $repositoryConfig = $repositories['default']['product_catalog'];
        self::assertIsArray($repositoryConfig);
        self::assertArrayHasKey('france', $repositoryConfig['regions']);

        $regionConfig = $repositoryConfig['regions']['france'];
        self::assertArrayHasKey('vat_categories', $regionConfig);
        self::assertSame($expectedVatCategories, $regionConfig['vat_categories']);
    }

    /**
     * @dataProvider provideForInvalidRegionConfigurationTest
     *
     * @param string|int|float $value
     */
    public function testInvalidRegionConfiguration($value): void
    {
        $config = $this->buildConfiguration([
            'regions' => [
                'france' => [
                    'vat_categories' => [
                        'foo' => [
                            'value' => $value,
                        ],
                    ],
                ],
            ],
        ]);

        $this->expectException(InvalidConfigurationException::class);
        $message = 'Invalid configuration for path'
            . ' "ibexa.repositories.default.product_catalog.regions.france.vat_categories.foo.value":'
            . ' VAT setting should be a numeric value between 0 and 100, or NULL';
        $this->expectExceptionMessage($message);

        $this->load($config);
    }

    /**
     * @return iterable<array{
     *     array<string, array<mixed>>,
     *     array<string, array<mixed>>,
     * }>
     */
    public function provideDataForTestRegionConfiguration(): iterable
    {
        $expected = [
            'foo' => [
                'value' => 12,
                'extras' => [],
            ],
            'bar' => [
                'value' => 0,
                'extras' => [
                    'not_applicable' => true,
                ],
            ],
            'fii' => [
                'value' => 0,
                'extras' => [],
            ],
        ];

        yield 'Old configuration - BC safe' => [
            $expected,
            $this->buildConfiguration(
                [
                    'regions' => [
                        'france' => [
                            'vat_categories' => [
                                'foo' => 12,
                                'bar' => null,
                                'fii' => 0,
                            ],
                        ],
                    ],
                ]
            ),
        ];

        $expected['baz'] = [
            'value' => 18,
            'extras' => [
                'foo' => 'foo',
                'bar' => 'bar',
                'baz' => 'baz',
            ],
        ];
        $expected['none'] = [
            'value' => 0,
            'extras' => [
                'foo' => 'foo',
                'not_applicable' => true,
            ],
        ];
        $expected['none_applicable'] = [
            'value' => 0,
            'extras' => [
                'not_applicable' => false,
                'foo' => 'foo',
            ],
        ];

        yield 'New complex configuration' => [
            $expected,
            $this->buildConfiguration(
                [
                    'regions' => [
                        'france' => [
                            'vat_categories' => [
                                'foo' => [
                                    'value' => 12,
                                ],
                                'bar' => [
                                    'value' => null,
                                ],
                                'fii' => [
                                    'value' => 0,
                                ],
                                'baz' => [
                                    'value' => 18,
                                    'extras' => [
                                        'foo' => 'foo',
                                        'bar' => 'bar',
                                        'baz' => 'baz',
                                    ],
                                ],
                                'none' => [
                                    'value' => null,
                                    'extras' => [
                                        'foo' => 'foo',
                                    ],
                                ],
                                'none_applicable' => [
                                    'value' => null,
                                    'extras' => [
                                        'not_applicable' => false,
                                        'foo' => 'foo',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]
            ),
        ];
    }

    /**
     * @return iterable<string, array{string|int|float}>}>
     */
    public function provideForInvalidRegionConfigurationTest(): iterable
    {
        yield 'Over 100 VAT' => [
            100.111,
        ];

        yield 'Below 0 VAT' => [
            -0.1,
        ];

        yield 'Non-numeric VAT' => [
            '_non_numeric_100',
        ];
    }

    /**
     * @param array<string, mixed> $repositoryConfig
     *
     * @return array<string, array<mixed>>
     */
    private function buildConfiguration(array $repositoryConfig): array
    {
        return [
            'repositories' => [
                'default' => [
                    'product_catalog' => $repositoryConfig,
                ],
            ],
        ];
    }
}
