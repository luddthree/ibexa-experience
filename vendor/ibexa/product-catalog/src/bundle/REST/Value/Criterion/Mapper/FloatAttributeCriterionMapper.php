<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value\Criterion\Mapper;

use Ibexa\Bundle\ProductCatalog\REST\Value\Criterion\FloatAttribute as RestFloatAttribute;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\FloatAttribute;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;

final class FloatAttributeCriterionMapper implements CriterionMapperInterface
{
    /**
     * @phpstan-param \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\FloatAttribute $criterion
     */
    public function mapToRest(CriterionInterface $criterion): RestFloatAttribute
    {
        return new RestFloatAttribute($criterion);
    }

    public function supports(CriterionInterface $criterion): bool
    {
        return $criterion instanceof FloatAttribute;
    }
}
