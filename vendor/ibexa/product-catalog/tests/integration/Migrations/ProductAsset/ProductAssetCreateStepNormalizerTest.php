<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Migrations\ProductAsset;

use Ibexa\ProductCatalog\Migrations\ProductAsset\ProductAssetCreateStep;
use Ibexa\Tests\Integration\ProductCatalog\Migrations\AbstractNormalizerTest;

/**
 * @covers \Ibexa\ProductCatalog\Migrations\ProductAsset\ProductAssetCreateStepNormalizer
 *
 * @extends \Ibexa\Tests\Integration\ProductCatalog\Migrations\AbstractNormalizerTest<
 *     \Ibexa\ProductCatalog\Migrations\ProductAsset\ProductAssetCreateStep
 * >
 */
final class ProductAssetCreateStepNormalizerTest extends AbstractNormalizerTest
{
    protected static function getHandledClass(): string
    {
        return ProductAssetCreateStep::class;
    }

    public function provideForSerialization(): iterable
    {
        yield [
            new ProductAssetCreateStep(
                'P0001',
                'ezcontent://1',
                [
                    'foo' => 10,
                    'bar' => true,
                    'baz' => 2,
                ]
            ),
            <<<YAML
            type: product_asset
            mode: create
            product_code: P0001
            uri: 'ezcontent://1'
            tags:
                foo: 10
                bar: true
                baz: 2

            YAML,
        ];
    }

    public function provideForDeserialization(): iterable
    {
        yield [
            <<<YAML
            type: product_asset
            mode: create
            product_code: P0001
            uri: "ezcontent://1"
            tags:
                foo: 10
                bar: true
                baz: 2

            YAML,
            static function (object $step): void {
                self::assertEquals(
                    new ProductAssetCreateStep(
                        'P0001',
                        'ezcontent://1',
                        [
                            'foo' => 10,
                            'bar' => true,
                            'baz' => 2,
                        ]
                    ),
                    $step
                );
            },
        ];
    }
}
