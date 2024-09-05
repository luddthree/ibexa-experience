<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository;

use Ibexa\Contracts\ProductCatalog\Local\LocalAssetServiceDecorator;
use Ibexa\Contracts\ProductCatalog\Local\LocalAssetServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\Values\Asset\AssetCreateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\Asset\AssetUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Asset\AssetCollectionInterface;
use Ibexa\Contracts\ProductCatalog\Values\Asset\AssetInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use PHPUnit\Framework\TestCase;

final class LocalAssetServiceDecoratorTest extends TestCase
{
    private const EXAMPLE_IDENTIFIER = 'main';

    /** @var \Ibexa\Contracts\ProductCatalog\Local\LocalAssetServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private LocalAssetServiceInterface $service;

    private LocalAssetServiceDecorator $decorator;

    protected function setUp(): void
    {
        $this->service = $this->createMock(LocalAssetServiceInterface::class);
        $this->decorator = $this->createDecorator($this->service);
    }

    public function testCreateAsset(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $createStruct = new AssetCreateStruct();
        $expectedResult = $this->createMock(AssetInterface::class);

        $this->service
            ->expects(self::once())
            ->method('createAsset')
            ->with($product, $createStruct)
            ->willReturn($expectedResult);

        $actualResult = $this->decorator->createAsset($product, $createStruct);

        self::assertEquals($expectedResult, $actualResult);
    }

    public function testDeleteAsset(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $asset = $this->createMock(AssetInterface::class);

        $this->service
            ->expects(self::once())
            ->method('deleteAsset')
            ->with($product, $asset);

        $this->decorator->deleteAsset($product, $asset);
    }

    public function testNewAssetCreateStruct(): void
    {
        $expectedResult = new AssetCreateStruct();

        $this->service
            ->expects(self::once())
            ->method('newAssetCreateStruct')
            ->willReturn($expectedResult);

        $actualResult = $this->decorator->newAssetCreateStruct();

        self::assertEquals($expectedResult, $actualResult);
    }

    public function testNewAssetUpdateStruct(): void
    {
        $expectedResult = new AssetUpdateStruct();

        $this->service
            ->expects(self::once())
            ->method('newAssetUpdateStruct')
            ->willReturn($expectedResult);

        $actualResult = $this->decorator->newAssetUpdateStruct();

        self::assertEquals($expectedResult, $actualResult);
    }

    public function testUpdateAsset(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $asset = $this->createMock(AssetInterface::class);
        $updateStruct = new AssetUpdateStruct();
        $expectedResult = $this->createMock(AssetInterface::class);

        $this->service
            ->expects(self::once())
            ->method('updateAsset')
            ->with($product, $asset, $updateStruct)
            ->willReturn($expectedResult);

        $actualResult = $this->decorator->updateAsset($product, $asset, $updateStruct);

        self::assertEquals($expectedResult, $actualResult);
    }

    public function testFindAssets(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $expectedResult = $this->createMock(AssetCollectionInterface::class);

        $this->service
            ->expects(self::once())
            ->method('findAssets')
            ->with($product)
            ->willReturn($expectedResult);

        $actualResult = $this->decorator->findAssets($product);

        self::assertEquals($expectedResult, $actualResult);
    }

    public function testGetAsset(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $expectedResult = $this->createMock(AssetInterface::class);

        $this->service
            ->expects(self::once())
            ->method('getAsset')
            ->with($product, self::EXAMPLE_IDENTIFIER)
            ->willReturn($expectedResult);

        $actualResult = $this->decorator->getAsset($product, self::EXAMPLE_IDENTIFIER);

        self::assertEquals($expectedResult, $actualResult);
    }

    private function createDecorator(LocalAssetServiceInterface $service): LocalAssetServiceDecorator
    {
        return new class($service) extends LocalAssetServiceDecorator {
            // Empty decorator implementation
        };
    }
}
