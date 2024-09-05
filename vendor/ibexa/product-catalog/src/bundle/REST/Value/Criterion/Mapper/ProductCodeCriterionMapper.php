<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value\Criterion\Mapper;

use Ibexa\Bundle\ProductCatalog\REST\Value\Criterion\ProductCode as RestProductCode;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductCode;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;

final class ProductCodeCriterionMapper implements CriterionMapperInterface
{
    /**
     * @phpstan-param \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductCode $criterion
     */
    public function mapToRest(CriterionInterface $criterion): RestProductCode
    {
        return new RestProductCode($criterion);
    }

    public function supports(CriterionInterface $criterion): bool
    {
        return $criterion instanceof ProductCode;
    }
}
