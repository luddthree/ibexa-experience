<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion;

final class ProductCriteriaRegistry
{
    /** @var iterable<\Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion\ProductCriterionInterface> */
    private iterable $criteria;

    /**
     * @param iterable<\Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion\ProductCriterionInterface> $criteria
     */
    public function __construct(iterable $criteria)
    {
        $this->criteria = $criteria;
    }

    /**
     * @return iterable<\Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion\ProductCriterionInterface>
     */
    public function getCriteria(): iterable
    {
        return $this->criteria;
    }
}
