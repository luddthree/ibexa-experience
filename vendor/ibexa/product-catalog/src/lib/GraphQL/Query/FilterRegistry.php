<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\GraphQL\Query;

/**
 * @internal
 */
final class FilterRegistry
{
    /** @var iterable<\Ibexa\ProductCatalog\GraphQL\Query\Filter\FilterInterface> */
    private iterable $filters;

    /**
     * @param iterable<\Ibexa\ProductCatalog\GraphQL\Query\Filter\FilterInterface> $filters
     */
    public function __construct(iterable $filters)
    {
        $this->filters = $filters;
    }

    /**
     * @return iterable<\Ibexa\ProductCatalog\GraphQL\Query\Filter\FilterInterface>
     */
    public function getFilters(): iterable
    {
        return $this->filters;
    }
}
