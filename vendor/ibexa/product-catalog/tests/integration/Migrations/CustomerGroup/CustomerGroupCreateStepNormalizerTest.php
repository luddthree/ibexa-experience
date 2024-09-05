<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Migrations\CustomerGroup;

use Ibexa\ProductCatalog\Migrations\CustomerGroup\CustomerGroupCreateStep;
use Ibexa\Tests\Integration\ProductCatalog\Migrations\AbstractNormalizerTest;

/**
 * @covers \Ibexa\ProductCatalog\Migrations\CustomerGroup\CustomerGroupCreateStepNormalizer
 *
 * @extends \Ibexa\Tests\Integration\ProductCatalog\Migrations\AbstractNormalizerTest<\Ibexa\ProductCatalog\Migrations\CustomerGroup\CustomerGroupCreateStep>
 */
final class CustomerGroupCreateStepNormalizerTest extends AbstractNormalizerTest
{
    protected static function getHandledClass(): string
    {
        return CustomerGroupCreateStep::class;
    }

    public function provideForSerialization(): iterable
    {
        yield [
            new CustomerGroupCreateStep('foo', [
                'eng-GB' => 'english name',
            ], [], '0'),
            <<<YAML
            type: customer_group
            mode: create
            identifier: foo
            names:
                eng-GB: 'english name'
            descriptions: {  }
            global_price_rate: '0'

            YAML,
        ];

        yield [
            new CustomerGroupCreateStep('bar', [
                'eng-GB' => 'english name',
                'eng-US' => 'american name',
            ], [
                'eng-US' => 'american description',
            ], '200.05'),
            <<<YAML
            type: customer_group
            mode: create
            identifier: bar
            names:
                eng-GB: 'english name'
                eng-US: 'american name'
            descriptions:
                eng-US: 'american description'
            global_price_rate: '200.05'

            YAML,
        ];
    }

    public function provideForDeserialization(): iterable
    {
        yield [
            <<<YAML
            type: customer_group
            mode: create
            identifier: foo
            names:
                eng-GB: english name
            YAML,
            static function (object $step): void {
                self::assertInstanceOf(CustomerGroupCreateStep::class, $step);
                self::assertSame('foo', $step->getIdentifier());
                self::assertSame([
                    'eng-GB' => 'english name',
                ], $step->getNames());
                self::assertSame([], $step->getDescriptions());
                self::assertSame('0', $step->getGlobalPriceRate());
            },
        ];

        yield [
            <<<YAML
            type: customer_group
            mode: create
            identifier: bar
            names:
                eng-GB: english name
                eng-US: american name
            descriptions:
                eng-US: american description
            global_price_rate: 200.05
            YAML,
            static function (object $step): void {
                self::assertInstanceOf(CustomerGroupCreateStep::class, $step);
                self::assertSame('bar', $step->getIdentifier());
                self::assertSame([
                    'eng-GB' => 'english name',
                    'eng-US' => 'american name',
                ], $step->getNames());
                self::assertSame([
                    'eng-US' => 'american description',
                ], $step->getDescriptions());
                self::assertSame('200.05', $step->getGlobalPriceRate());
            },
        ];
    }
}
