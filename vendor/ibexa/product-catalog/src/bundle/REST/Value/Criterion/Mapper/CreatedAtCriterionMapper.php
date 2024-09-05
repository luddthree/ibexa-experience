<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value\Criterion\Mapper;

use Ibexa\Bundle\ProductCatalog\REST\Value\Criterion\CreatedAt as RestCreatedAt;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\CreatedAt;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;

final class CreatedAtCriterionMapper implements CriterionMapperInterface
{
    /**
     * @phpstan-param \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\CreatedAt $criterion
     */
    public function mapToRest(CriterionInterface $criterion): RestCreatedAt
    {
        return new RestCreatedAt($criterion);
    }

    public function supports(CriterionInterface $criterion): bool
    {
        return $criterion instanceof CreatedAt;
    }
}
