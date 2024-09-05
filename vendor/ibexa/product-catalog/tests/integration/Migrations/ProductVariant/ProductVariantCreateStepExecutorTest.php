<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Migrations\ProductVariant;

use Ibexa\Contracts\ProductCatalog\Local\LocalProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\ProductCatalog\Migrations\ProductVariant\ProductVariantCreateStep;
use Ibexa\ProductCatalog\Migrations\ProductVariant\ProductVariantCreateStepEntry;
use Ibexa\ProductCatalog\Migrations\ProductVariant\ProductVariantCreateStepExecutor;
use Ibexa\Tests\Integration\ProductCatalog\Migrations\AbstractStepExecutorTest;

/**
 * @covers \Ibexa\ProductCatalog\Migrations\ProductVariant\ProductVariantCreateStepExecutor
 */
final class ProductVariantCreateStepExecutorTest extends AbstractStepExecutorTest
{
    private const EXAMPLE_BASE_PRODUCT_CODE = '0003';
    private const EXAMPLE_VARIANT_CODE_A = self::EXAMPLE_BASE_PRODUCT_CODE . '_v1';
    private const EXAMPLE_VARIANT_CODE_B = self::EXAMPLE_BASE_PRODUCT_CODE . '_v2';
    private const EXAMPLE_VARIANT_CODE_C = self::EXAMPLE_BASE_PRODUCT_CODE . '_v3';

    private LocalProductServiceInterface $productService;

    private ProductVariantCreateStepExecutor $executor;

    protected function setUp(): void
    {
        self::setAdministratorUser();

        $this->productService = self::getServiceByClassName(LocalProductServiceInterface::class);
        $this->executor = new ProductVariantCreateStepExecutor($this->productService);
        $this->configureExecutor($this->executor);
    }

    public function testCanHandleProductVariantCreateStep(): void
    {
        $step = new ProductVariantCreateStep(
            self::EXAMPLE_BASE_PRODUCT_CODE,
            [/* argument is not relevant for the test */]
        );

        self::assertTrue($this->executor->canHandle($step));
    }

    public function testCannotHandleOtherSteps(): void
    {
        self::assertFalse($this->executor->canHandle($this->createMock(StepInterface::class)));
    }

    public function testHandle(): void
    {
        $step = new ProductVariantCreateStep(
            self::EXAMPLE_BASE_PRODUCT_CODE,
            [
                new ProductVariantCreateStepEntry(
                    ['bar' => true, 'baz' => 1],
                    self::EXAMPLE_VARIANT_CODE_A
                ),
                new ProductVariantCreateStepEntry(
                    ['bar' => true, 'baz' => 2],
                    self::EXAMPLE_VARIANT_CODE_B
                ),
                new ProductVariantCreateStepEntry(
                    ['bar' => true, 'baz' => 3],
                    self::EXAMPLE_VARIANT_CODE_C
                ),
            ]
        );

        $this->executor->handle($step);

        $baseProduct = $this->productService->getProduct(self::EXAMPLE_BASE_PRODUCT_CODE);

        $variants = $this->productService->findProductVariants($baseProduct);

        self::assertEquals(
            [
                self::EXAMPLE_VARIANT_CODE_A,
                self::EXAMPLE_VARIANT_CODE_B,
                self::EXAMPLE_VARIANT_CODE_C,
            ],
            array_map(
                static fn (ProductVariantInterface $variant): string => $variant->getCode(),
                $variants->getVariants()
            )
        );
    }
}
