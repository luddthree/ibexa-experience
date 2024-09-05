<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ActivityLog\REST\Input\Parser\SortClause;

use Ibexa\Contracts\ActivityLog\Values\ActivityLog\SortClause\LoggedAtSortClause;

final class LoggedAtTest extends AbstractSortClauseParserTestCase
{
    public static function provideXml(): iterable
    {
        yield [
            <<<XML
            <sortClause type="logged_at">DESC</sortClause>
            XML,
            new LoggedAtSortClause('DESC'),
        ];

        yield [
            <<<XML
            <sortClause type="logged_at">
                <direction>DESC</direction>
            </sortClause>
            XML,
            new LoggedAtSortClause('DESC'),
        ];

        yield [
            <<<XML
            <sortClause type="logged_at" />
            XML,
            new LoggedAtSortClause('ASC'),
        ];
    }

    public static function provideJson(): iterable
    {
        yield [
            <<<JSON
            {"type": "logged_at", "direction": "DESC"}
            JSON,
            new LoggedAtSortClause('DESC'),
        ];

        yield [
            <<<JSON
            {"type": "logged_at"}
            JSON,
            new LoggedAtSortClause('ASC'),
        ];
    }
}
