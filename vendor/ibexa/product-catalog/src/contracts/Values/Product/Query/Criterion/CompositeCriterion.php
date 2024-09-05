<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;

abstract class CompositeCriterion implements CriterionInterface
{
    private CriterionInterface $criteria;

    public function __construct(CriterionInterface $criteria)
    {
        $this->criteria = $criteria;
    }

    public function getInnerCriteria(): CriterionInterface
    {
        return $this->criteria;
    }
}
