<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ActivityLog\REST\Input\Parser\Criterion;

use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\LoggedAtCriterion;

/**
 * @covers \Ibexa\ActivityLog\REST\Input\Parser\Criterion\LoggedAt
 */
final class LoggedAtTest extends AbstractCriterionParserTestCase
{
    public static function provideXml(): iterable
    {
        yield [
            <<<XML
            <criterion type="logged_at">
                <value>2023-10-03 10:50:00</value>
                <operator>&gt;</operator>
            </criterion>
            XML,
            new LoggedAtCriterion(new \DateTimeImmutable('2023-10-03 10:50:00'), '>'),
        ];

        yield [
            <<<XML
            <criterion type="logged_at">
                <value>2023-10-03 14:50:00</value>
                <operator>&lt;</operator>
            </criterion>
            XML,
            new LoggedAtCriterion(new \DateTimeImmutable('2023-10-03 14:50:00'), '<'),
        ];
    }

    public static function provideJson(): iterable
    {
        yield [
            <<<JSON
            {"type": "logged_at", "value": "2023-10-03 10:50:00", "operator": ">"}
            JSON,
            new LoggedAtCriterion(new \DateTimeImmutable('2023-10-03 10:50:00'), '>'),
        ];

        yield [
            <<<JSON
            {"type": "logged_at", "value": "2023-10-03 14:50:00", "operator": "<"}
            JSON,
            new LoggedAtCriterion(new \DateTimeImmutable('2023-10-03 14:50:00'), '<'),
        ];
    }
}
