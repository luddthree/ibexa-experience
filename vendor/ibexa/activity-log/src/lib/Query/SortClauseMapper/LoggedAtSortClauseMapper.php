<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\Query\SortClauseMapper;

use Ibexa\Contracts\ActivityLog\SortClauseMapperInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\SortClause\LoggedAtSortClause;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\SortClause\SortClauseInterface;

/**
 * @implements \Ibexa\Contracts\ActivityLog\SortClauseMapperInterface<\Ibexa\Contracts\ActivityLog\Values\ActivityLog\SortClause\LoggedAtSortClause>
 */
final class LoggedAtSortClauseMapper implements SortClauseMapperInterface
{
    public function canHandle(SortClauseInterface $sortClause): bool
    {
        return $sortClause instanceof LoggedAtSortClause;
    }

    public function handle(SortClauseInterface $sortClause): array
    {
        return [
            'logged_at' => $sortClause->getOrder(),
        ];
    }
}
