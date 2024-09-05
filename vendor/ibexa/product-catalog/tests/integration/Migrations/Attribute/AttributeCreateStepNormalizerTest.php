<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Migrations\Attribute;

use Ibexa\ProductCatalog\Migrations\Attribute\AttributeCreateStep;
use Ibexa\Tests\Integration\ProductCatalog\Migrations\AbstractNormalizerTest;

/**
 * @covers \Ibexa\ProductCatalog\Migrations\Attribute\AttributeCreateStepNormalizer
 *
 * @extends \Ibexa\Tests\Integration\ProductCatalog\Migrations\AbstractNormalizerTest<\Ibexa\ProductCatalog\Migrations\Attribute\AttributeCreateStep>
 */
final class AttributeCreateStepNormalizerTest extends AbstractNormalizerTest
{
    protected static function getHandledClass(): string
    {
        return AttributeCreateStep::class;
    }

    public function provideForSerialization(): iterable
    {
        yield [
            new AttributeCreateStep(
                'foo_identifier',
                'foo_attribute_group_identifier',
                'foo_attribute_type_identifier',
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
            mode: create
            identifier: foo_identifier
            attribute_group_identifier: foo_attribute_group_identifier
            attribute_type_identifier: foo_attribute_type_identifier
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
            mode: create
            identifier: foo_identifier
            attribute_group_identifier: foo_attribute_group_identifier
            attribute_type_identifier: foo_attribute_type_identifier
            position: 42
            names:
                foo_country: foo_country_name
                bar_country: bar_country_name
            descriptions:
                foo_country: foo_country_description

            YAML,
            static function (object $step): void {
                self::assertInstanceOf(AttributeCreateStep::class, $step);
                self::assertSame('foo_identifier', $step->getIdentifier());
                self::assertSame('foo_attribute_group_identifier', $step->getAttributeGroupIdentifier());
                self::assertSame('foo_attribute_type_identifier', $step->getAttributeTypeIdentifier());
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
