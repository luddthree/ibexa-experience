<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\VatCategory;

use Ibexa\Bundle\Core\ApiLoader\RepositoryConfigurationProvider;
use Ibexa\ProductCatalog\Local\Repository\Values\VatCategory;

final class VatCategoryPoolFactory implements VatCategoryPoolFactoryInterface
{
    private RepositoryConfigurationProvider $repositoryConfigProvider;

    public function __construct(RepositoryConfigurationProvider $repositoryConfigProvider)
    {
        $this->repositoryConfigProvider = $repositoryConfigProvider;
    }

    public function createPool(): VatCategoryPoolInterface
    {
        $pool = [];
        foreach ($this->getVatCategoriesConfig() as $region => $categories) {
            $pool[$region] = [];

            foreach ($categories as $category => $config) {
                $pool[$region][$category] = new VatCategory($region, $category, $config['value']);
            }
        }

        return new VatCategoryPool($pool);
    }

    /**
     * @return iterable<string,array<string, array{
     *     value: float,
     *     extras: array<string>
     * }>>
     */
    private function getVatCategoriesConfig(): iterable
    {
        $repositoryConfig = $this->repositoryConfigProvider->getRepositoryConfig();

        $config = $repositoryConfig['product_catalog']['regions'] ?? [];

        foreach ($config as $identifier => $region) {
            yield $identifier => $region['vat_categories'] ?? [];
        }
    }
}
