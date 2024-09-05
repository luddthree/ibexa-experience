<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value\Criterion\Mapper;

use Ibexa\Bundle\ProductCatalog\REST\Value\Criterion\IntegerAttributeRange as RestIntegerAttributeRange;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\IntegerAttributeRange;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;

final class IntegerAttributeRangeCriterionMapper implements CriterionMapperInterface
{
    /**
     * @phpstan-param \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\IntegerAttributeRange $criterion
     */
    public function mapToRest(CriterionInterface $criterion): RestIntegerAttributeRange
    {
        return new RestIntegerAttributeRange($criterion);
    }

    public function supports(CriterionInterface $criterion): bool
    {
        return $criterion instanceof IntegerAttributeRange;
    }
}
