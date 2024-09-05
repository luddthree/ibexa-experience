<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\SortClauseVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;
use Ibexa\Contracts\Elasticsearch\Query\SortClauseVisitor;
use RuntimeException;

abstract class AbstractSortClauseVisitor implements SortClauseVisitor
{
    protected function getDirection(SortClause $sortClause): string
    {
        switch ($sortClause->direction) {
            case Query::SORT_ASC:
                return 'asc';
            case Query::SORT_DESC:
                return 'desc';
            default:
                throw new RuntimeException('Invalid sort direction: ' . $sortClause->direction);
        }
    }
}

class_alias(AbstractSortClauseVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\SortClauseVisitor\AbstractSortClauseVisitor');
