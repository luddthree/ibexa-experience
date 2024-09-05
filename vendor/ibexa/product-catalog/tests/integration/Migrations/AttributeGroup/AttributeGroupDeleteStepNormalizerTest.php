<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Migrations\AttributeGroup;

use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion;
use Ibexa\ProductCatalog\Migrations\AttributeGroup\AttributeGroupDeleteStep;
use Ibexa\Tests\Integration\ProductCatalog\Migrations\AbstractNormalizerTest;

/**
 * @covers \Ibexa\ProductCatalog\Migrations\AttributeGroup\AttributeGroupDeleteStepNormalizer
 *
 * @extends \Ibexa\Tests\Integration\ProductCatalog\Migrations\AbstractNormalizerTest<
 *     \Ibexa\ProductCatalog\Migrations\AttributeGroup\AttributeGroupDeleteStep
 * >
 */
final class AttributeGroupDeleteStepNormalizerTest extends AbstractNormalizerTest
{
    protected static function getHandledClass(): string
    {
        return AttributeGroupDeleteStep::class;
    }

    public function provideForSerialization(): iterable
    {
        yield [
            new AttributeGroupDeleteStep(
                new FieldValueCriterion('identifier', 'bar_identifier'),
            ),
            <<<YAML
            type: attribute_group
            mode: delete
            criteria:
                type: field_value
                field: identifier
                value: bar_identifier
                operator: '='

            YAML,
        ];
    }

    public function provideForDeserialization(): iterable
    {
        yield [
            <<<YAML
            type: attribute_group
            mode: delete
            criteria:
                type: field_value
                field: foo_field
                value: bar_identifier

            YAML,
            static function (object $step): void {
                self::assertInstanceOf(AttributeGroupDeleteStep::class, $step);
                $criterion = $step->getCriterion();
                self::assertInstanceOf(FieldValueCriterion::class, $criterion);
                self::assertSame('foo_field', $criterion->getField());
                self::assertSame('bar_identifier', $criterion->getValue());
            },
        ];
    }
}
