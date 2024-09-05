<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\Query;

use Ibexa\Contracts\ActivityLog\SortClauseMapperInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\SortClause\SortClauseInterface;
use LogicException;

/**
 * @implements \Ibexa\Contracts\ActivityLog\SortClauseMapperInterface<\Ibexa\Contracts\ActivityLog\Values\ActivityLog\SortClause\SortClauseInterface>
 */
final class SortClauseMapper implements SortClauseMapperInterface
{
    /** @var iterable<\Ibexa\Contracts\ActivityLog\SortClauseMapperInterface<\Ibexa\Contracts\ActivityLog\Values\ActivityLog\SortClause\SortClauseInterface>> */
    private iterable $mappers;

    /**
     * @param iterable<\Ibexa\Contracts\ActivityLog\SortClauseMapperInterface<\Ibexa\Contracts\ActivityLog\Values\ActivityLog\SortClause\SortClauseInterface>> $mappers
     */
    public function __construct(iterable $mappers)
    {
        $this->mappers = $mappers;
    }

    /**
     * @phpstan-return array<string, 'ASC'|'DESC'>
     */
    public function handle(SortClauseInterface $sortClause): array
    {
        foreach ($this->mappers as $mapper) {
            if ($mapper->canHandle($sortClause)) {
                return $mapper->handle($sortClause);
            }
        }

        throw new LogicException(sprintf(
            'Unable to handle "%s" sort clause. '
            . 'Ensure that a %s service exists for this clause and is tagged with %s',
            get_class($sortClause),
            SortClauseMapperInterface::class,
            'ibexa.activity_log.query.sort_clause_mapper',
        ));
    }

    public function canHandle(SortClauseInterface $sortClause): bool
    {
        foreach ($this->mappers as $mapper) {
            if ($mapper->canHandle($sortClause)) {
                return true;
            }
        }

        return false;
    }
}
