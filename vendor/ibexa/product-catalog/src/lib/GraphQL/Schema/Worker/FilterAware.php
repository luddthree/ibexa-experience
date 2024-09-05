<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\GraphQL\Schema\Worker;

use Ibexa\GraphQL\Schema\Builder;
use Ibexa\ProductCatalog\GraphQL\Query\FilterRegistry;

abstract class FilterAware extends Base
{
    protected FilterRegistry $filterCollection;

    public function __construct(FilterRegistry $filterCollection)
    {
        $this->filterCollection = $filterCollection;
    }

    public function addFilters(Builder $schema, string $type, string $field): void
    {
        foreach ($this->filterCollection->getFilters() as $filter) {
            $schema->addArgToField(
                $type,
                $field,
                new Builder\Input\Arg(
                    $filter->getName(),
                    $filter->getType(),
                    ['description' => $filter->getDescription()]
                )
            );
        }
    }

    public function addSortClauses(Builder $schema, string $type, string $field): void
    {
        $schema->addArgToField(
            $type,
            $field,
            new Builder\Input\Arg(
                'sortBy',
                '[SortByProductOptions]',
                ['description' => 'A Sort Clause, or array of Clauses. Add _desc after a Clause to reverse it']
            )
        );
    }
}
