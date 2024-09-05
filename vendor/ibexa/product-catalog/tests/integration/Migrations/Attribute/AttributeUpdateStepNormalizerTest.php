<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Migrations\Attribute;

use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion;
use Ibexa\ProductCatalog\Migrations\Attribute\AttributeUpdateStep;
use Ibexa\Tests\Integration\ProductCatalog\Migrations\AbstractNormalizerTest;

/**
 * @covers \Ibexa\ProductCatalog\Migrations\Attribute\AttributeUpdateStepNormalizer
 *
 * @extends \Ibexa\Tests\Integration\ProductCatalog\Migrations\AbstractNormalizerTest<\Ibexa\ProductCatalog\Migrations\Attribute\AttributeUpdateStep>
 */
final class AttributeUpdateStepNormalizerTest extends AbstractNormalizerTest
{
    protected static function getHandledClass(): string
    {
        return AttributeUpdateStep::class;
    }

    public function provideForSerialization(): iterable
    {
        yield [
            new AttributeUpdateStep(
                new FieldValueCriterion('identifier', 'bar_identifier'),
                'foo_identifier',
                'foo_attribute_group_identifier',
                42,
                [
                    'foo_country' => 'foo_country_name',
                    'bar_country' => 'bar_country_name',
                ],
                [
                    'foo_country' => 'foo_country_description',
                ],
                [],
            ),
            <<<YAML
            type: attribute
            mode: update
            criteria:
                type: field_value
                field: identifier
                value: bar_identifier
                operator: '='
            identifier: foo_identifier
            attribute_group_identifier: foo_attribute_group_identifier
            position: 42
            names:
                foo_country: foo_country_name
                bar_country: bar_country_name
            descriptions:
                foo_country: foo_country_description
            options: {  }

            YAML,
        ];
    }

    public function provideForDeserialization(): iterable
    {
        yield [
            <<<YAML
            type: attribute
            mode: update
            criteria:
                type: field_value
                field: identifier
                value: bar_identifier
            identifier: foo_identifier
            attribute_group_identifier: foo_attribute_group_identifier
            position: 42
            names:
                foo_country: foo_country_name
                bar_country: bar_country_name
            descriptions:
                foo_country: foo_country_description

            YAML,
            static function (object $step): void {
                self::assertInstanceOf(AttributeUpdateStep::class, $step);
                $criterion = $step->getCriterion();
                self::assertInstanceOf(FieldValueCriterion::class, $criterion);
                self::assertSame('identifier', $criterion->getField());
                self::assertSame('bar_identifier', $criterion->getValue());
                self::assertSame('foo_identifier', $step->getIdentifier());
                self::assertSame('foo_attribute_group_identifier', $step->getAttributeGroupIdentifier());
                self::assertSame(42, $step->getPosition());
                self::assertSame([
                    'foo_country' => 'foo_country_name',
                    'bar_country' => 'bar_country_name',
                ], $step->getNames());
                self::assertSame([
                    'foo_country' => 'foo_country_description',
                ], $step->getDescriptions());
                self::assertSame([], $step->getOptions());
            },
        ];
    }
}
