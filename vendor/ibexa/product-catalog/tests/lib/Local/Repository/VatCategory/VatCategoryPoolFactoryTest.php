<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Repository\VatCategory;

use Ibexa\Bundle\Core\ApiLoader\RepositoryConfigurationProvider;
use Ibexa\ProductCatalog\Local\Repository\Values\VatCategory;
use Ibexa\ProductCatalog\Local\Repository\VatCategory\VatCategoryPoolFactory;
use PHPUnit\Framework\TestCase;

final class VatCategoryPoolFactoryTest extends TestCase
{
    public const EXAMPLE_REPOSITORY_CONFIG = [
        'product_catalog' => [
            'regions' => [
                'foo' => [
                    'vat_categories' => [
                        'standard' => [
                            'value' => 25,
                        ],
                        'reduced' => [
                            'value' => 15,
                        ],
                        'zero' => [
                            'value' => 0,
                        ],
                    ],
                ],
            ],
        ],
    ];

    public function testCreatePool(): void
    {
        $repositoryConfigProvider = $this->createMock(RepositoryConfigurationProvider::class);
        $repositoryConfigProvider
            ->method('getRepositoryConfig')
            ->willReturn(self::EXAMPLE_REPOSITORY_CONFIG);

        $factory = new VatCategoryPoolFactory($repositoryConfigProvider);

        $pool = $factory->createPool();

        self::assertEquals(
            [
                'standard' => new VatCategory('foo', 'standard', 25.0),
                'reduced' => new VatCategory('foo', 'reduced', 15.0),
                'zero' => new VatCategory('foo', 'zero', 0.0),
            ],
            $pool->getVatCategories('foo'),
        );
    }
}
