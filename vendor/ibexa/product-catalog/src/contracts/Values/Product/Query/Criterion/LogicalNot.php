<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;

final class LogicalNot implements CriterionInterface
{
    private CriterionInterface $criterion;

    public function __construct(CriterionInterface $criterion)
    {
        $this->criterion = $criterion;
    }

    public function getCriterion(): CriterionInterface
    {
        return $this->criterion;
    }
}
