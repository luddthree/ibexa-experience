<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ActivityLog;

use Ibexa\Contracts\ActivityLog\Values\ActivityLog\SortClause\SortClauseInterface;

/**
 * @template T of \Ibexa\Contracts\ActivityLog\Values\ActivityLog\SortClause\SortClauseInterface
 */
interface SortClauseMapperInterface
{
    public function canHandle(SortClauseInterface $sortClause): bool;

    /**
     * @phpstan-param T $sortClause
     *
     * @phpstan-return array<string, SortClauseInterface::DESC|SortClauseInterface::ASC>
     */
    public function handle(SortClauseInterface $sortClause): array;
}
