<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Migrations\ProductVariant;

use Ibexa\ProductCatalog\Migrations\ProductVariant\ProductVariantCreateStep;
use Ibexa\ProductCatalog\Migrations\ProductVariant\ProductVariantCreateStepEntry;
use Ibexa\Tests\Integration\ProductCatalog\Migrations\AbstractNormalizerTest;

/**
 * @covers \Ibexa\ProductCatalog\Migrations\ProductVariant\ProductVariantCreateStepNormalizer
 *
 * @extends \Ibexa\Tests\Integration\ProductCatalog\Migrations\AbstractNormalizerTest<
 *     \Ibexa\ProductCatalog\Migrations\ProductVariant\ProductVariantCreateStep
 * >
 */
final class ProductVariantCreateStepNormalizerTest extends AbstractNormalizerTest
{
    protected static function getHandledClass(): string
    {
        return ProductVariantCreateStep::class;
    }

    public function provideForSerialization(): iterable
    {
        yield [
            new ProductVariantCreateStep(
                '123',
                [
                    new ProductVariantCreateStepEntry(
                        [
                            'foo' => 'foo',
                            'bar' => 'bar',
                            'baz' => 'baz',
                        ],
                        'VAR_1'
                    ),
                    new ProductVariantCreateStepEntry(
                        [
                            'foo' => 'foo',
                            'bar' => 'bar',
                            'baz' => 'baz',
                        ],
                        'VAR_2'
                    ),
                ]
            ),
            <<<YAML
            type: product_variant
            mode: create
            base_product_code: '123'
            variants:
                -
                    code: VAR_1
                    attributes:
                        foo: foo
                        bar: bar
                        baz: baz
                -
                    code: VAR_2
                    attributes:
                        foo: foo
                        bar: bar
                        baz: baz

            YAML,
        ];
    }

    public function provideForDeserialization(): iterable
    {
        yield 'all fields' => [
            <<<YAML
            type: product_variant
            mode: create
            base_product_code: '123'
            variants:
                -
                    code: VAR_1
                    attributes:
                        foo: foo
                        bar: bar
                        baz: baz
                -
                    code: VAR_2
                    attributes:
                        foo: foo
                        bar: bar
                        baz: baz
            YAML,
            static function (object $step): void {
                self::assertEquals(
                    new ProductVariantCreateStep(
                        '123',
                        [
                            new ProductVariantCreateStepEntry(
                                [
                                    'foo' => 'foo',
                                    'bar' => 'bar',
                                    'baz' => 'baz',
                                ],
                                'VAR_1'
                            ),
                            new ProductVariantCreateStepEntry(
                                [
                                    'foo' => 'foo',
                                    'bar' => 'bar',
                                    'baz' => 'baz',
                                ],
                                'VAR_2'
                            ),
                        ]
                    ),
                    $step
                );
            },
        ];

        yield 'empty or missing variant code' => [
            <<<YAML
            type: product_variant
            mode: create
            base_product_code: '123'
            variants:
                -
                    code: ~
                    attributes:
                        foo: foo
                        bar: bar
                        baz: baz
                -
                    attributes:
                        foo: foo
                        bar: bar
                        baz: baz
            YAML,
            static function (object $step): void {
                self::assertEquals(
                    new ProductVariantCreateStep(
                        '123',
                        [
                            new ProductVariantCreateStepEntry(
                                [
                                    'foo' => 'foo',
                                    'bar' => 'bar',
                                    'baz' => 'baz',
                                ],
                                null
                            ),
                            new ProductVariantCreateStepEntry(
                                [
                                    'foo' => 'foo',
                                    'bar' => 'bar',
                                    'baz' => 'baz',
                                ],
                                null
                            ),
                        ]
                    ),
                    $step
                );
            },
        ];
    }
}
