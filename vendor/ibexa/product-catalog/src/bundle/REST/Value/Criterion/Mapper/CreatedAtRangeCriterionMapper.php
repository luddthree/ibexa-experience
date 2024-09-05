<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value\Criterion\Mapper;

use Ibexa\Bundle\ProductCatalog\REST\Value\Criterion\CreatedAtRange as RestCreatedAtRange;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\CreatedAtRange;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;

final class CreatedAtRangeCriterionMapper implements CriterionMapperInterface
{
    /**
     * @phpstan-param \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\CreatedAtRange $criterion
     */
    public function mapToRest(CriterionInterface $criterion): RestCreatedAtRange
    {
        return new RestCreatedAtRange($criterion);
    }

    public function supports(CriterionInterface $criterion): bool
    {
        return $criterion instanceof CreatedAtRange;
    }
}
