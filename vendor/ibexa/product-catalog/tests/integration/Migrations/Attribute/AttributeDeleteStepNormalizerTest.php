<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Migrations\Attribute;

use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion;
use Ibexa\ProductCatalog\Migrations\Attribute\AttributeDeleteStep;
use Ibexa\Tests\Integration\ProductCatalog\Migrations\AbstractNormalizerTest;

/**
 * @covers \Ibexa\ProductCatalog\Migrations\Attribute\AttributeDeleteStepNormalizer
 *
 * @extends \Ibexa\Tests\Integration\ProductCatalog\Migrations\AbstractNormalizerTest<\Ibexa\ProductCatalog\Migrations\Attribute\AttributeDeleteStep>
 */
final class AttributeDeleteStepNormalizerTest extends AbstractNormalizerTest
{
    protected static function getHandledClass(): string
    {
        return AttributeDeleteStep::class;
    }

    public function provideForSerialization(): iterable
    {
        yield [
            new AttributeDeleteStep(
                new FieldValueCriterion('identifier', 'bar_identifier'),
            ),
            <<<YAML
            type: attribute
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
            type: attribute
            mode: delete
            criteria:
                type: field_value
                field: identifier
                value: bar_identifier

            YAML,
            static function (object $step): void {
                self::assertInstanceOf(AttributeDeleteStep::class, $step);
                $criterion = $step->getCriterion();
                self::assertInstanceOf(FieldValueCriterion::class, $criterion);
                self::assertSame('identifier', $criterion->getField());
                self::assertSame('bar_identifier', $criterion->getValue());
            },
        ];
    }
}
