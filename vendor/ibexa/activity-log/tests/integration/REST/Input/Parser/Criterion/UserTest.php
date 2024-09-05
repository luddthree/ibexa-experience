<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ActivityLog\REST\Input\Parser\Criterion;

use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\UserCriterion;

/**
 * @covers \Ibexa\ActivityLog\REST\Input\Parser\Criterion\User
 */
final class UserTest extends AbstractCriterionParserTestCase
{
    public static function provideXml(): iterable
    {
        yield [
            <<<XML
            <criterion type="user">
                <value>14</value>
            </criterion>
            XML,
            new UserCriterion([14]),
        ];

        yield [
            <<<XML
            <criterion type="user">
                <value>14</value>
                <value>42</value>
            </criterion>
            XML,
            new UserCriterion([14, 42]),
        ];

        yield [
            <<<XML
            <criterion type="user">14</criterion>
            XML,
            new UserCriterion([14]),
        ];
    }

    public static function provideJson(): iterable
    {
        yield [
            <<<JSON
            {"type": "user", "value": [14]}
            JSON,
            new UserCriterion([14]),
        ];

        yield [
            <<<JSON
            {"type": "user", "value": [14,42]}
            JSON,
            new UserCriterion([14, 42]),
        ];

        yield [
            <<<JSON
            {"type": "user", "value": 14}
            JSON,
            new UserCriterion([14]),
        ];
    }
}
