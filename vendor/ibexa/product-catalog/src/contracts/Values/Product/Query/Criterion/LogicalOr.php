<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;

final class LogicalOr implements CriterionInterface
{
    /** @var \Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface[] */
    private array $criteria;

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface[] $criteria
     */
    public function __construct(array $criteria)
    {
        $this->criteria = $criteria;
    }

    /**
     * @return \Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface[]
     */
    public function getCriteria(): array
    {
        return $this->criteria;
    }
}
