<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Migrations\AttributeGroup;

use Ibexa\ProductCatalog\Migrations\AttributeGroup\AttributeGroupCreateStep;
use Ibexa\Tests\Integration\ProductCatalog\Migrations\AbstractNormalizerTest;

/**
 * @covers \Ibexa\ProductCatalog\Migrations\AttributeGroup\AttributeGroupCreateStepNormalizer
 *
 * @extends \Ibexa\Tests\Integration\ProductCatalog\Migrations\AbstractNormalizerTest<
 *     \Ibexa\ProductCatalog\Migrations\AttributeGroup\AttributeGroupCreateStep
 * >
 */
final class AttributeGroupCreateStepNormalizerTest extends AbstractNormalizerTest
{
    protected static function getHandledClass(): string
    {
        return AttributeGroupCreateStep::class;
    }

    public function provideForSerialization(): iterable
    {
        yield [
            new AttributeGroupCreateStep(
                'foo_identifier',
                [
                    'foo_country' => 'foo_country_name',
                    'bar_country' => 'bar_country_name',
                ],
                42,
            ),
            <<<YAML
            type: attribute_group
            mode: create
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
            mode: create
            identifier: foo_identifier
            position: 42
            names:
                foo_country: foo_country_name
                bar_country: bar_country_name

            YAML,
            static function (object $step): void {
                self::assertInstanceOf(AttributeGroupCreateStep::class, $step);
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
