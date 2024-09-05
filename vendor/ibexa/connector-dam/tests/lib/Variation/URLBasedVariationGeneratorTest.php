<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Connector\Dam\Variation;

use Ibexa\Connector\Dam\Variation\URLBasedVariationGenerator;
use Ibexa\Contracts\Connector\Dam\Asset;
use Ibexa\Contracts\Connector\Dam\AssetIdentifier;
use Ibexa\Contracts\Connector\Dam\AssetMetadata;
use Ibexa\Contracts\Connector\Dam\AssetSource;
use Ibexa\Contracts\Connector\Dam\AssetUri;
use Ibexa\Contracts\Connector\Dam\Variation\Transformation;
use PHPUnit\Framework\TestCase;

class URLBasedVariationGeneratorTest extends TestCase
{
    /**
     * @dataProvider variationGeneratorProvider
     */
    public function testGenerate(
        AssetUri $original,
        Transformation $transformation,
        AssetUri $expected
    ): void {
        $id = new AssetIdentifier('test_id');
        $source = new AssetSource('test_source');
        $assetMetadata = new AssetMetadata([]);

        $generator = new URLBasedVariationGenerator();

        $asset = new Asset(
            $id,
            $source,
            $original,
            $assetMetadata
        );

        $result = $generator->generate(
            $asset,
            $transformation
        );

        $this->assertEquals(
            $result->getAssetUri(),
            $expected
        );
    }

    public function variationGeneratorProvider(): array
    {
        return [
            [
                new AssetUri('http://example.com/test.jpg'),
                new Transformation(null, [
                    'param_1' => 'value_1',
                    'param_2' => 'value_2',
                ]),
                new AssetUri('http://example.com/test.jpg?param_1=value_1&param_2=value_2'),
            ],
            [
                new AssetUri('http://example.com/test.jpg?entry_param=123'),
                new Transformation(null, [
                    'param_1' => 'value_1',
                    'param_2' => 'value_2',
                ]),
                new AssetUri('http://example.com/test.jpg?entry_param=123&param_1=value_1&param_2=value_2'),
            ],
        ];
    }
}

class_alias(URLBasedVariationGeneratorTest::class, 'Ibexa\Platform\Tests\Connector\Dam\Variation\URLBasedVariationGeneratorTest');
