<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ActivityLog\Values\ActivityLog;

final class Query
{
    public const DEFAULT_LIMIT = 25;

    public int $offset;

    public int $limit;

    /** @var list<\Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\CriterionInterface> */
    public array $criteria;

    /** @var list<\Ibexa\Contracts\ActivityLog\Values\ActivityLog\SortClause\SortClauseInterface> */
    public array $sortClauses;

    /**
     * @param list<\Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\CriterionInterface> $criteria An AND list of criteria
     * @param list<\Ibexa\Contracts\ActivityLog\Values\ActivityLog\SortClause\SortClauseInterface> $sortClauses
     */
    public function __construct(
        array $criteria = [],
        array $sortClauses = [],
        int $offset = 0,
        int $limit = self::DEFAULT_LIMIT
    ) {
        $this->criteria = $criteria;
        $this->sortClauses = $sortClauses;
        $this->offset = $offset;
        $this->limit = $limit;
    }
}
