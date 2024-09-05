<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value\Criterion\Mapper;

use Ibexa\Bundle\ProductCatalog\REST\Value\Criterion\SelectionAttribute as RestSelectionAttribute;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\SelectionAttribute;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;

final class SelectionAttributeCriterionMapper implements CriterionMapperInterface
{
    /**
     * @phpstan-param \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\SelectionAttribute $criterion
     */
    public function mapToRest(CriterionInterface $criterion): RestSelectionAttribute
    {
        return new RestSelectionAttribute($criterion);
    }

    public function supports(CriterionInterface $criterion): bool
    {
        return $criterion instanceof SelectionAttribute;
    }
}
