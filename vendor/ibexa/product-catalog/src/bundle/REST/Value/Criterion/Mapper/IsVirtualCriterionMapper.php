<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value\Criterion\Mapper;

use Ibexa\Bundle\ProductCatalog\REST\Value\Criterion\IsVirtual as RestIsVirtual;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\IsVirtual;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;

final class IsVirtualCriterionMapper implements CriterionMapperInterface
{
    /**
     * @phpstan-param \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\IsVirtual $criterion
     */
    public function mapToRest(CriterionInterface $criterion): RestIsVirtual
    {
        return new RestIsVirtual($criterion);
    }

    public function supports(CriterionInterface $criterion): bool
    {
        return $criterion instanceof IsVirtual;
    }
}
