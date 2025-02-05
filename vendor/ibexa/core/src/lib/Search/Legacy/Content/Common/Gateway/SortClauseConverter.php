<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Search\Legacy\Content\Common\Gateway;

use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use RuntimeException;

/**
 * Converter manager for sort clauses.
 */
class SortClauseConverter
{
    /**
     * Sort clause handlers.
     *
     * @var \Ibexa\Core\Search\Legacy\Content\Common\Gateway\SortClauseHandler[]
     */
    protected $handlers;

    /**
     * Sorting information for temporary sort columns.
     *
     * @var array
     */
    protected $sortColumns = [];

    /**
     * Construct from an optional array of sort clause handlers.
     *
     * @param \Ibexa\Core\Search\Legacy\Content\Common\Gateway\SortClauseHandler[] $handlers
     */
    public function __construct(array $handlers = [])
    {
        $this->handlers = $handlers;
    }

    /**
     * Adds handler.
     *
     * @param \Ibexa\Core\Search\Legacy\Content\Common\Gateway\SortClauseHandler $handler
     */
    public function addHandler(SortClauseHandler $handler)
    {
        $this->handlers[] = $handler;
    }

    /**
     * Apply select parts of sort clauses to query.
     *
     * @param \Doctrine\DBAL\Query\QueryBuilder $query
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause[] $sortClauses
     *
     * @throws \RuntimeException If no handler is available for sort clause
     */
    public function applySelect(QueryBuilder $query, array $sortClauses): void
    {
        foreach ($sortClauses as $nr => $sortClause) {
            foreach ($this->handlers as $handler) {
                if ($handler->accept($sortClause)) {
                    foreach ($handler->applySelect($query, $sortClause, $nr) as $column) {
                        if (strrpos($column, '_null', -6) === false) {
                            $direction = $sortClause->direction;
                        } else {
                            // Always sort null last
                            $direction = 'ASC';
                        }

                        $this->sortColumns[$column] = $direction;
                    }
                    continue 2;
                }
            }

            throw new RuntimeException('No handler available for Sort Clause: ' . get_class($sortClause));
        }
    }

    /**
     * Apply join parts of sort clauses to query.
     *
     * @throws \RuntimeException If no handler is available for sort clause
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause[] $sortClauses
     * @param array $languageSettings
     */
    public function applyJoin(QueryBuilder $query, array $sortClauses, array $languageSettings): void
    {
        foreach ($sortClauses as $nr => $sortClause) {
            foreach ($this->handlers as $handler) {
                if ($handler->accept($sortClause)) {
                    $handler->applyJoin($query, $sortClause, $nr, $languageSettings);
                    continue 2;
                }
            }

            throw new RuntimeException('No handler available for Sort Clause: ' . get_class($sortClause));
        }
    }

    /**
     * Apply order by parts of sort clauses to query.
     */
    public function applyOrderBy(QueryBuilder $query): void
    {
        foreach ($this->sortColumns as $column => $direction) {
            $query->addOrderBy(
                $column,
                $direction === Query::SORT_ASC ? 'ASC' : 'DESC'
            );
        }
        // @todo Review needed
        // The following line was added because without it, loading sub user groups through the Public API
        // fails with the database error "Unknown column sort_column_0". The change does not break any
        // integration tests or legacy persistence tests, but it can break something else, so review is needed
        // Discussion: https://github.com/ezsystems/ezpublish-kernel/commit/8749d0977307858c3e2a7d82f3be90fa21973357#L1R102
        $this->sortColumns = [];
    }
}

class_alias(SortClauseConverter::class, 'eZ\Publish\Core\Search\Legacy\Content\Common\Gateway\SortClauseConverter');
