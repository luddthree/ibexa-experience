<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Repository\Region;

use Ibexa\Bundle\Core\ApiLoader\RepositoryConfigurationProvider;
use Ibexa\ProductCatalog\Local\Repository\Region\RegionPoolFactory;
use Ibexa\ProductCatalog\Local\Repository\Values\Region;
use PHPUnit\Framework\TestCase;

final class RegionPoolFactoryTest extends TestCase
{
    private const EXAMPLE_REPOSITORY_CONFIG = [
        'product_catalog' => [
            'regions' => [
                'foo' => [],
                'bar' => [],
                'baz' => [],
            ],
        ],
    ];

    public function testCreatePool(): void
    {
        $configProvider = $this->createMock(RepositoryConfigurationProvider::class);
        $configProvider->method('getRepositoryConfig')->willReturn(self::EXAMPLE_REPOSITORY_CONFIG);

        $factory = new RegionPoolFactory($configProvider);

        self::assertEquals([
            'foo' => new Region('foo'),
            'bar' => new Region('bar'),
            'baz' => new Region('baz'),
        ], $factory->createPool()->getRegions());
    }
}
