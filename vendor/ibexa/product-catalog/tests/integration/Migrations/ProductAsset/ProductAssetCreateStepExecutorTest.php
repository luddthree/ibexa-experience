<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Migrations\ProductAsset;

use Ibexa\Contracts\ProductCatalog\Local\LocalAssetServiceInterface;
use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\ProductCatalog\Migrations\ProductAsset\ProductAssetCreateStep;
use Ibexa\ProductCatalog\Migrations\ProductAsset\ProductAssetCreateStepExecutor;
use Ibexa\Tests\Integration\ProductCatalog\Migrations\AbstractStepExecutorTest;

/**
 * @covers \Ibexa\ProductCatalog\Migrations\ProductAsset\ProductAssetCreateStepExecutor
 */
final class ProductAssetCreateStepExecutorTest extends AbstractStepExecutorTest
{
    private const EXAMPLE_PRODUCT_CODE = '0003';
    private const EXAMPLE_ASSET_URI = 'ezcontent://0';
    private const EXAMPLE_TAGS = [
        'foo' => 10,
        'bar' => true,
        'baz' => 2,
    ];

    private ProductServiceInterface $productService;

    private LocalAssetServiceInterface $assetService;

    private ProductAssetCreateStepExecutor $executor;

    protected function setUp(): void
    {
        self::setAdministratorUser();

        $this->productService = self::getServiceByClassName(ProductServiceInterface::class);
        $this->assetService = self::getServiceByClassName(LocalAssetServiceInterface::class);

        $this->executor = new ProductAssetCreateStepExecutor($this->assetService, $this->productService);
        $this->configureExecutor($this->executor);
    }

    public function testCanHandleProductAssetCreateStep(): void
    {
        $step = new ProductAssetCreateStep(
            self::EXAMPLE_PRODUCT_CODE,
            'ezcontent://0',
            [
                'foo' => 10,
                'bar' => true,
                'baz' => 2,
            ]
        );

        self::assertTrue($this->executor->canHandle($step));
    }

    public function testCannotHandleOtherSteps(): void
    {
        self::assertFalse($this->executor->canHandle($this->createMock(StepInterface::class)));
    }

    public function testHandle(): void
    {
        $step = new ProductAssetCreateStep(
            self::EXAMPLE_PRODUCT_CODE,
            self::EXAMPLE_ASSET_URI,
            self::EXAMPLE_TAGS
        );

        $product = $this->productService->getProduct(self::EXAMPLE_PRODUCT_CODE);

        $this->executor->handle($step);

        /** @var \Ibexa\Contracts\ProductCatalog\Values\Asset\AssetInterface $asset */
        $asset = $this->assetService->findAssets($product)->last();

        self::assertEquals(self::EXAMPLE_ASSET_URI, $asset->getUri());
        self::assertEquals(self::EXAMPLE_TAGS, $asset->getTags());
    }
}
