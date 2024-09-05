<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value\Criterion\Mapper;

use Ibexa\Bundle\ProductCatalog\REST\Value\Criterion\ProductCategory as RestProductCategory;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductCategory;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;

final class ProductCategoryCriterionMapper implements CriterionMapperInterface
{
    /**
     * @phpstan-param \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductCategory $criterion
     */
    public function mapToRest(CriterionInterface $criterion): RestProductCategory
    {
        return new RestProductCategory($criterion);
    }

    public function supports(CriterionInterface $criterion): bool
    {
        return $criterion instanceof ProductCategory;
    }
}
