<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Migrations\CustomerGroup;

use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\Query\Criterion\CustomerGroupIdentifier;
use Ibexa\ProductCatalog\Migrations\CustomerGroup\CustomerGroupUpdateStep;
use Ibexa\Tests\Integration\ProductCatalog\Migrations\AbstractNormalizerTest;

/**
 * @covers \Ibexa\ProductCatalog\Migrations\CustomerGroup\CustomerGroupUpdateStepNormalizer
 *
 * @extends \Ibexa\Tests\Integration\ProductCatalog\Migrations\AbstractNormalizerTest<\Ibexa\ProductCatalog\Migrations\CustomerGroup\CustomerGroupUpdateStep>
 */
final class CustomerGroupUpdateStepNormalizerTest extends AbstractNormalizerTest
{
    public static function getHandledClass(): string
    {
        return CustomerGroupUpdateStep::class;
    }

    public function provideForSerialization(): iterable
    {
        yield [
            new CustomerGroupUpdateStep(
                new CustomerGroupIdentifier('foo'),
                null,
                [],
                [],
                null,
            ),
            <<<YAML
            type: customer_group
            mode: update
            criteria:
                type: field_value
                field: identifier
                value: foo
                operator: '='
            identifier: null
            names: {  }
            descriptions: {  }
            global_price_rate: null

            YAML
        ];

        yield [
            new CustomerGroupUpdateStep(
                new FieldValueCriterion('name', 'bar', 'CONTAINS'),
                'foo',
                [
                    'eng-GB' => 'english name',
                    'eng-US' => 'american name',
                ],
                [
                    'eng-US' => 'american description',
                ],
                '200.05',
            ),
            <<<YAML
            type: customer_group
            mode: update
            criteria:
                type: field_value
                field: name
                value: bar
                operator: CONTAINS
            identifier: foo
            names:
                eng-GB: 'english name'
                eng-US: 'american name'
            descriptions:
                eng-US: 'american description'
            global_price_rate: '200.05'

            YAML
        ];
    }

    public function provideForDeserialization(): iterable
    {
        $yaml = <<<YAML
            type: customer_group
            mode: update
            criteria:
                type: field_value
                field: identifier
                value: foo
            identifier: ~
            names: { }
            descriptions: { }
            global_price_rate: ~
            YAML;

        $expectation = static function (object $step): void {
            self::assertInstanceOf(CustomerGroupUpdateStep::class, $step);
            $criterion = $step->getCriterion();
            self::assertInstanceOf(FieldValueCriterion::class, $criterion);
            self::assertSame('identifier', $criterion->getField());
            self::assertSame('foo', $criterion->getValue());
            self::assertSame('=', $criterion->getOperator());

            self::assertNull($step->getIdentifier());
            self::assertSame([], $step->getNames());
            self::assertSame([], $step->getDescriptions());
            self::assertNull($step->getGlobalPriceRate());
        };

        yield [
            $yaml,
            $expectation,
        ];

        $yaml = <<<YAML
            type: customer_group
            mode: update
            criteria:
                type: field_value
                field: identifier
                value: foo
            YAML;

        yield [
            $yaml,
            $expectation,
        ];

        $yaml = <<<YAML
            type: customer_group
            mode: update
            criteria:
                type: field_value
                field: name
                value: bar
                operator: CONTAINS
            identifier: foo
            names:
                eng-GB: 'english name'
                eng-US: 'american name'
            descriptions:
                eng-US: 'american description'
            global_price_rate: '200.05'
            YAML;

        $expectation = static function (object $step): void {
            self::assertInstanceOf(CustomerGroupUpdateStep::class, $step);
            $criterion = $step->getCriterion();
            self::assertInstanceOf(FieldValueCriterion::class, $criterion);
            self::assertSame('name', $criterion->getField());
            self::assertSame('bar', $criterion->getValue());
            self::assertSame('CONTAINS', $criterion->getOperator());

            self::assertSame('foo', $step->getIdentifier());
            self::assertSame([
                'eng-GB' => 'english name',
                'eng-US' => 'american name',
            ], $step->getNames());
            self::assertSame([
                'eng-US' => 'american description',
            ], $step->getDescriptions());
            self::assertSame('200.05', $step->getGlobalPriceRate());
        };

        yield [
            $yaml,
            $expectation,
        ];
    }
}
