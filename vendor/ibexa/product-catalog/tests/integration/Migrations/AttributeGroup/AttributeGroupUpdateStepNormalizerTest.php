<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Migrations\AttributeGroup;

use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion;
use Ibexa\ProductCatalog\Migrations\AttributeGroup\AttributeGroupUpdateStep;
use Ibexa\Tests\Integration\ProductCatalog\Migrations\AbstractNormalizerTest;

/**
 * @covers \Ibexa\ProductCatalog\Migrations\AttributeGroup\AttributeGroupUpdateStepNormalizer
 *
 * @extends \Ibexa\Tests\Integration\ProductCatalog\Migrations\AbstractNormalizerTest<
 *     \Ibexa\ProductCatalog\Migrations\AttributeGroup\AttributeGroupUpdateStep
 * >
 */
final class AttributeGroupUpdateStepNormalizerTest extends AbstractNormalizerTest
{
    protected static function getHandledClass(): string
    {
        return AttributeGroupUpdateStep::class;
    }

    public function provideForSerialization(): iterable
    {
        yield [
            new AttributeGroupUpdateStep(
                new FieldValueCriterion('identifier', 'bar_identifier'),
                'foo_identifier',
                [
                    'foo_country' => 'foo_country_name',
                    'bar_country' => 'bar_country_name',
                ],
                42,
            ),
            <<<YAML
            type: attribute_group
            mode: update
            criteria:
                type: field_value
                field: identifier
                value: bar_identifier
                operator: '='
            identifier: foo_identifier
            position: 42
            names:
                foo_country: foo_country_name
                bar_country: bar_country_name

            YAML,
        ];
    }

    public function provideForDeserialization(): iterable
    {
        yield [
            <<<YAML
            type: attribute_group
            mode: update
            criteria:
                type: field_value
                field: foo_field
                value: bar_identifier

            YAML,
            static function (object $step): void {
                self::assertInstanceOf(AttributeGroupUpdateStep::class, $step);
                $criterion = $step->getCriterion();
                self::assertInstanceOf(FieldValueCriterion::class, $criterion);
                self::assertSame('foo_field', $criterion->getField());
                self::assertSame('bar_identifier', $criterion->getValue());
                self::assertNull($step->getIdentifier());
                self::assertNull($step->getPosition());
                self::assertSame([], $step->getNames());
            },
        ];

        yield [
            <<<YAML
            type: attribute_group
            mode: update
            criteria:
                type: field_value
                field: foo_field
                value: bar_identifier
            identifier: foo_identifier
            position: 42
            names:
                foo_country: foo_country_name
                bar_country: bar_country_name

            YAML,
            static function (object $step): void {
                self::assertInstanceOf(AttributeGroupUpdateStep::class, $step);
                $criterion = $step->getCriterion();
                self::assertInstanceOf(FieldValueCriterion::class, $criterion);
                self::assertSame('foo_field', $criterion->getField());
                self::assertSame('bar_identifier', $criterion->getValue());
                self::assertSame('foo_identifier', $step->getIdentifier());
                self::assertSame(42, $step->getPosition());
                self::assertSame([
                    'foo_country' => 'foo_country_name',
                    'bar_country' => 'bar_country_name',
                ], $step->getNames());
            },
        ];
    }
}
