<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ActivityLog\Query;

use Ibexa\ActivityLog\Query\SortClauseMapper;
use Ibexa\Contracts\ActivityLog\SortClauseMapperInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\SortClause\SortClauseInterface;
use LogicException;
use PHPUnit\Framework\TestCase;

final class SortClauseMapperTest extends TestCase
{
    public function testThrowsExceptionIfSortClauseUnhandled(): void
    {
        $criterionMapper = new SortClauseMapper([]);

        $sortClause = $this->getMockBuilder(SortClauseInterface::class)
            ->setMockClassName('SortClauseClass')
            ->getMock();

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(
            'Unable to handle "SortClauseClass" sort clause. '
            . 'Ensure that a ' . SortClauseMapperInterface::class . ' service exists for this '
            . 'clause and is tagged with ibexa.activity_log.query.sort_clause_mapper'
        );
        $criterionMapper->handle($sortClause);
    }
}
