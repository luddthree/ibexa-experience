<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Region;

use Ibexa\Bundle\Core\ApiLoader\RepositoryConfigurationProvider;
use Ibexa\ProductCatalog\Local\Repository\Values\Region;

final class RegionPoolFactory implements RegionPoolFactoryInterface
{
    private RepositoryConfigurationProvider $repositoryConfigProvider;

    public function __construct(RepositoryConfigurationProvider $repositoryConfigProvider)
    {
        $this->repositoryConfigProvider = $repositoryConfigProvider;
    }

    public function createPool(): RegionPoolInterface
    {
        $pool = [];
        foreach ($this->getRegionIdentifiers() as $identifier) {
            $pool[$identifier] = new Region($identifier);
        }

        return new RegionPool($pool);
    }

    /**
     * @return string[]
     */
    private function getRegionIdentifiers(): array
    {
        $config = $this->repositoryConfigProvider->getRepositoryConfig();

        return array_keys($config['product_catalog']['regions'] ?? []);
    }
}
