<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository;

use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductVariantCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Tests\Integration\ProductCatalog\IbexaKernelTestCase;

final class AssetServiceTest extends IbexaKernelTestCase
{
    private const EXISTING_ASSET_IDENTIFIER = '1';
    private const NON_EXISTING_ASSET_IDENTIFIER = '0';

    protected function setUp(): void
    {
        parent::setUp();

        self::setAdministratorUser();
    }

    public function testCreateAsset(): void
    {
        $assetService = self::getLocalAssetService();

        $createStruct = $assetService->newAssetCreateStruct();
        $createStruct->setUri('ezcontent://4');
        $createStruct->setTags([
            'foo' => 10,
            'bar' => true,
            'baz' => 2,
        ]);

        $asset = $assetService->createAsset(self::getExampleProduct(), $createStruct);

        self::assertEquals('ezcontent://4', $asset->getUri());
        self::assertEquals(
            [
                'foo' => 10,
                'bar' => true,
                'baz' => 2,
            ],
            $asset->getTags()
        );
    }

    public function testCreateAssetValidateURI(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument \'$createStruct->uri\' is invalid: malformed URI');

        $assetService = self::getLocalAssetService();
        $emptyCreateStruct = $assetService->newAssetCreateStruct();
        $assetService->createAsset(self::getExampleProduct(), $emptyCreateStruct);
    }

    public function testDeleteAsset(): void
    {
        $assetService = self::getLocalAssetService();

        $product = self::getExampleProduct();

        $asset = $assetService->getAsset($product, self::EXISTING_ASSET_IDENTIFIER);
        $assetService->deleteAsset($product, $asset);

        self::assertAssetDoesNotExist(self::EXISTING_ASSET_IDENTIFIER);
    }

    public function testUpdateAsset(): void
    {
        $assetService = self::getLocalAssetService();

        $product = self::getExampleProduct();

        $asset = $assetService->getAsset($product, self::EXISTING_ASSET_IDENTIFIER);
        self::assertEquals('ezcontent://1', $asset->getUri());
        self::assertEquals([], $asset->getTags());

        $updateStruct = $assetService->newAssetUpdateStruct();
        $updateStruct->setUri('ezcontent://5');
        $updateStruct->setTags(['a', 'b', 'c']);

        $updatedAsset = $assetService->updateAsset($product, $asset, $updateStruct);

        self::assertEquals('ezcontent://5', $updatedAsset->getUri());
        self::assertEquals(['a', 'b', 'c'], $updateStruct->getTags());
    }

    public function testUpdateAssetValidateURI(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument \'$updateStruct->uri\' is invalid: malformed URI');

        $assetService = self::getLocalAssetService();

        $product = self::getExampleProduct();

        $updateStruct = $assetService->newAssetUpdateStruct();
        $updateStruct->setUri('');

        $asset = $assetService->getAsset($product, self::EXISTING_ASSET_IDENTIFIER);

        $assetService->updateAsset($product, $asset, $updateStruct);
    }

    public function testFindAssets(): void
    {
        $assets = self::getAssetsService()->findAssets(self::getExampleProduct());

        self::assertAssets(
            ['ezcontent://1', 'ezcontent://2', 'ezcontent://3'],
            $assets
        );
    }

    public function testFindAssetsForProductVariants(): void
    {
        $assets = self::getAssetsService()->findAssets(self::getExampleProductVariant());

        self::assertAssets(
            ['ezcontent://1', 'ezcontent://2', 'ezcontent://3'],
            $assets
        );
    }

    public function testGetAsset(): void
    {
        $asset = self::getAssetsService()->getAsset(
            self::getExampleProduct(),
            self::EXISTING_ASSET_IDENTIFIER
        );

        self::assertEquals($asset->getUri(), 'ezcontent://1');
    }

    public function testGetAssetThrowsNotFoundException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage("Could not find 'Ibexa\Contracts\ProductCatalog\Values\ProductInterface' with identifier '0'");

        self::getAssetsService()->getAsset(
            self::getExampleProduct(),
            self::NON_EXISTING_ASSET_IDENTIFIER
        );
    }

    private static function getExampleProduct(): ProductInterface
    {
        return self::getProductService()->getProduct('0001');
    }

    private static function getExampleProductVariant(): ProductInterface
    {
        $productService = self::getLocalProductService();
        try {
            return $productService->getProduct('example-variant');
        } catch (NotFoundException $e) {
            $productService->createProductVariants(
                self::getExampleProduct(),
                [
                    new ProductVariantCreateStruct(
                        [
                            'bar' => true,
                            'baz' => 10,
                        ],
                        'example-variant'
                    ),
                ]
            );

            return $productService->getProduct('example-variant');
        }
    }

    private static function assertAssetDoesNotExist(string $identifier): void
    {
        try {
            self::getAssetsService()->getAsset(self::getExampleProduct(), $identifier);
            $found = true;
        } catch (NotFoundException $e) {
            $found = false;
        }

        self::assertFalse($found);
    }

    /**
     * @param string[] $expectedUris
     * @param \Ibexa\Contracts\ProductCatalog\Values\Asset\AssetInterface[] $assets
     */
    private static function assertAssets(array $expectedUris, iterable $assets): void
    {
        $actualUris = [];
        foreach ($assets as $asset) {
            $actualUris[] = $asset->getUri();
        }

        self::assertEquals($expectedUris, $actualUris);
    }
}
