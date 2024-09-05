<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository;

use Ibexa\Bundle\Core\ApiLoader\RepositoryConfigurationProvider;
use Ibexa\Contracts\ProductCatalog\Values\RegionInterface;
use Ibexa\Contracts\ProductCatalog\Values\VatCategory\VatCategoryListInterface;
use Ibexa\Contracts\ProductCatalog\Values\VatCategoryInterface;
use Ibexa\Contracts\ProductCatalog\VatServiceInterface;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\ProductCatalog\Local\Repository\Values\VatCategory;
use Ibexa\ProductCatalog\Local\Repository\Values\VatCategoryList;

/**
 * @todo Refactor to use VatCategoryProvider
 */
final class VatService implements VatServiceInterface
{
    private RepositoryConfigurationProvider $repositoryConfigProvider;

    public function __construct(
        RepositoryConfigurationProvider $repositoryConfigProvider
    ) {
        $this->repositoryConfigProvider = $repositoryConfigProvider;
    }

    public function getVatCategories(RegionInterface $region): VatCategoryListInterface
    {
        $repositoryConfig = $this->repositoryConfigProvider->getRepositoryConfig();
        $vatCategoriesConfig = $repositoryConfig['product_catalog']['regions'][$region->getIdentifier()]['vat_categories'];

        $list = [];
        foreach ($vatCategoriesConfig as $vatCategory => $vatConfig) {
            $list[] = new VatCategory($region->getIdentifier(), $vatCategory, $vatConfig['value']);
        }

        return new VatCategoryList($list, count($list));
    }

    public function getVatCategoryByIdentifier(RegionInterface $region, string $identifier): VatCategoryInterface
    {
        $repositoryConfig = $this->repositoryConfigProvider->getRepositoryConfig();
        $vatCategoriesConfig = $repositoryConfig['product_catalog']['regions'][$region->getIdentifier()]['vat_categories'];

        foreach ($vatCategoriesConfig as $vatCategory => $vatConfig) {
            if ($vatCategory === $identifier) {
                return new VatCategory($region->getIdentifier(), $identifier, $vatConfig['value']);
            }
        }

        throw new NotFoundException(VatCategoryInterface::class, $identifier);
    }
}
