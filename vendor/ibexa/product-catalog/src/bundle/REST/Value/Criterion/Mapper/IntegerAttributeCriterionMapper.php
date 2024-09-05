<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value\Criterion\Mapper;

use Ibexa\Bundle\ProductCatalog\REST\Value\Criterion\IntegerAttribute as RestIntegerAttribute;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\IntegerAttribute;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;

final class IntegerAttributeCriterionMapper implements CriterionMapperInterface
{
    /**
     * @phpstan-param \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\IntegerAttribute $criterion
     */
    public function mapToRest(CriterionInterface $criterion): RestIntegerAttribute
    {
        return new RestIntegerAttribute($criterion);
    }

    public function supports(CriterionInterface $criterion): bool
    {
        return $criterion instanceof IntegerAttribute;
    }
}
