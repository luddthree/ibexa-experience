<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value\Criterion\Mapper;

use Ibexa\Bundle\ProductCatalog\REST\Value\Criterion\ProductAvailability as RestProductAvailability;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductAvailability;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;

final class ProductAvailabilityCriterionMapper implements CriterionMapperInterface
{
    /**
     * @phpstan-param \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductAvailability $criterion
     */
    public function mapToRest(CriterionInterface $criterion): RestProductAvailability
    {
        return new RestProductAvailability($criterion);
    }

    public function supports(CriterionInterface $criterion): bool
    {
        return $criterion instanceof ProductAvailability;
    }
}
