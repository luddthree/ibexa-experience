<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value\Criterion\Mapper;

use Ibexa\Bundle\ProductCatalog\REST\Value\Criterion\FloatAttributeRange as RestFloatAttributeRange;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\FloatAttributeRange;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;

final class FloatAttributeRangeCriterionMapper implements CriterionMapperInterface
{
    /**
     * @phpstan-param \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\FloatAttributeRange $criterion
     */
    public function mapToRest(CriterionInterface $criterion): RestFloatAttributeRange
    {
        return new RestFloatAttributeRange($criterion);
    }

    public function supports(CriterionInterface $criterion): bool
    {
        return $criterion instanceof FloatAttributeRange;
    }
}
