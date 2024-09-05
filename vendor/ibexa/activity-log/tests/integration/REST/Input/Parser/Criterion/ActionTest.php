<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ActivityLog\REST\Input\Parser\Criterion;

use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\ActionCriterion;

/**
 * @covers \Ibexa\ActivityLog\REST\Input\Parser\Criterion\Action
 */
final class ActionTest extends AbstractCriterionParserTestCase
{
    public static function provideXml(): iterable
    {
        yield [
            <<<XML
            <criterion type="action">
                <value>bar_action</value>
            </criterion>
            XML,
            new ActionCriterion(['bar_action']),
        ];

        yield [
            <<<XML
            <criterion type="action">
                <value>foo_action</value>
                <value>bar_action</value>
            </criterion>
            XML,
            new ActionCriterion(['foo_action', 'bar_action']),
        ];

        yield [
            <<<XML
            <criterion type="action">bar_action</criterion>
            XML,
            new ActionCriterion(['bar_action']),
        ];
    }

    public static function provideJson(): iterable
    {
        yield [
            <<<JSON
            {"type": "action", "value": ["bar_action", "foo_action"]}
            JSON,
            new ActionCriterion(['bar_action', 'foo_action']),
        ];

        yield [
            <<<JSON
            {"type": "action", "value": "bar_action"}
            JSON,
            new ActionCriterion(['bar_action']),
        ];
    }
}
