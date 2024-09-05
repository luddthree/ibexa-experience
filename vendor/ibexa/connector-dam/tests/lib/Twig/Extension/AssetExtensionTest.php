<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Connector\Dam\Twig\Extension;

use Ibexa\Connector\Dam\Twig\Extension\AssetExtension;
use Ibexa\Contracts\Connector\Dam\Asset;
use Ibexa\Contracts\Connector\Dam\AssetIdentifier;
use Ibexa\Contracts\Connector\Dam\AssetMetadata;
use Ibexa\Contracts\Connector\Dam\AssetService;
use Ibexa\Contracts\Connector\Dam\AssetSource;
use Ibexa\Contracts\Connector\Dam\AssetUri;
use Ibexa\Contracts\Connector\Dam\ExternalAsset;
use Ibexa\Contracts\Connector\Dam\Variation\AssetVariation;
use Ibexa\Contracts\Connector\Dam\Variation\Transformation;
use PHPUnit\Framework\TestCase;

class AssetExtensionTest extends TestCase
{
    public function testGetAsset(): void
    {
        $assetServiceMock = $this->createMock(AssetService::class);

        $assetServiceMock
            ->method('get')
            ->willReturn(
                new Asset(
                    new AssetIdentifier('test_id'),
                    new AssetSource('test_source'),
                    new AssetUri(''),
                    new AssetMetadata([])
                )
            );

        $extension = new AssetExtension($assetServiceMock);

        $asset = $extension->getAsset('test_id', 'test_source');

        $this->assertInstanceOf(
            Asset::class,
            $asset
        );
    }

    public function testGetAssetVariation(): void
    {
        $assetServiceMock = $this->createMock(AssetService::class);
        $originalAsset = new Asset(
            new AssetIdentifier('test_id'),
            new AssetSource('test_source'),
            new AssetUri('location'),
            new AssetMetadata([])
        );
        $transformation = new Transformation(null, ['param_1' => 'value_1']);
        $assetServiceMock
            ->method('get')
            ->willReturn($originalAsset);

        $assetServiceMock
            ->method('transform')
            ->willReturn(
                new AssetVariation(
                    $originalAsset,
                    new AssetUri('transformed_location'),
                    $transformation
                )
            );

        $extension = new AssetExtension($assetServiceMock);

        $asset = $extension->getAsset(
            'test_id',
            'test_source',
            $transformation
        );

        $this->assertInstanceOf(
            ExternalAsset::class,
            $asset
        );
    }
}

class_alias(AssetExtensionTest::class, 'Ibexa\Platform\Tests\Connector\Dam\Twig\Extension\AssetExtensionTest');
