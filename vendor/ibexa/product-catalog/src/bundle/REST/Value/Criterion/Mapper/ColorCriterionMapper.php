<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value\Criterion\Mapper;

use Ibexa\Bundle\ProductCatalog\REST\Value\Criterion\ColorAttribute as RestColorAttribute;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ColorAttribute;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;

final class ColorCriterionMapper implements CriterionMapperInterface
{
    /**
     * @phpstan-param \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ColorAttribute $criterion
     */
    public function mapToRest(CriterionInterface $criterion): RestColorAttribute
    {
        return new RestColorAttribute($criterion);
    }

    public function supports(CriterionInterface $criterion): bool
    {
        return $criterion instanceof ColorAttribute;
    }
}
