<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ActivityLog\REST\Input\Parser\Criterion;

use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\ObjectCriterion;

/**
 * @covers \Ibexa\ActivityLog\REST\Input\Parser\Criterion\ObjectClass
 */
final class ObjectClassTest extends AbstractCriterionParserTestCase
{
    public static function provideXml(): iterable
    {
        yield [
            <<<XML
            <criterion type="object_class">
                <class>stdClass</class>
            </criterion>
            XML,
            new ObjectCriterion('stdClass'),
        ];

        yield [
            <<<XML
            <criterion type="object_class">
                <class>stdClass</class>
                <ids>bar</ids>
            </criterion>
            XML,
            new ObjectCriterion('stdClass', ['bar']),
        ];

        yield [
            <<<XML
            <criterion type="object_class">
                <class>stdClass</class>
                <ids>
                    <id>foo</id>
                    <id>bar</id>
                </ids>
            </criterion>
            XML,
            new ObjectCriterion('stdClass', ['foo', 'bar']),
        ];
    }

    public static function provideJson(): iterable
    {
        yield [
            <<<JSON
            {"type": "object_class", "class": "stdClass"}
            JSON,
            new ObjectCriterion('stdClass'),
        ];

        yield [
            <<<JSON
            {"type": "object_class", "class": "stdClass", "ids": ["bar"]}
            JSON,
            new ObjectCriterion('stdClass', ['bar']),
        ];

        yield [
            <<<JSON
            {"type": "object_class", "class": "stdClass", "ids": "bar"}
            JSON,
            new ObjectCriterion('stdClass', ['bar']),
        ];
    }
}
