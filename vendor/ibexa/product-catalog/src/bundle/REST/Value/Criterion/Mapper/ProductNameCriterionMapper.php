<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value\Criterion\Mapper;

use Ibexa\Bundle\ProductCatalog\REST\Value\Criterion\ProductName as RestProductName;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductName;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;

final class ProductNameCriterionMapper implements CriterionMapperInterface
{
    /**
     * @phpstan-param \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductName $criterion
     */
    public function mapToRest(CriterionInterface $criterion): RestProductName
    {
        return new RestProductName($criterion);
    }

    public function supports(CriterionInterface $criterion): bool
    {
        return $criterion instanceof ProductName;
    }
}
