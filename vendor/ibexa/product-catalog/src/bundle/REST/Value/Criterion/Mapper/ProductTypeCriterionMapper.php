<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value\Criterion\Mapper;

use Ibexa\Bundle\ProductCatalog\REST\Value\Criterion\ProductType as RestProductType;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductType;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;

final class ProductTypeCriterionMapper implements CriterionMapperInterface
{
    /**
     * @phpstan-param \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductType $criterion
     */
    public function mapToRest(CriterionInterface $criterion): RestProductType
    {
        return new RestProductType($criterion);
    }

    public function supports(CriterionInterface $criterion): bool
    {
        return $criterion instanceof ProductType;
    }
}
