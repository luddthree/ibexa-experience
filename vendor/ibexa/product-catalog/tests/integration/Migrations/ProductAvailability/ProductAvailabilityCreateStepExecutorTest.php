<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Migrations\ProductAvailability;

use Ibexa\Contracts\ProductCatalog\ProductAvailabilityServiceInterface;
use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\ProductCatalog\Migrations\ProductAvailability\ProductAvailabilityCreateStep;
use Ibexa\ProductCatalog\Migrations\ProductAvailability\ProductAvailabilityCreateStepExecutor;
use Ibexa\Tests\Integration\ProductCatalog\Migrations\AbstractStepExecutorTest;

/**
 * @covers \Ibexa\ProductCatalog\Migrations\ProductAvailability\ProductAvailabilityCreateStepExecutor
 */
final class ProductAvailabilityCreateStepExecutorTest extends AbstractStepExecutorTest
{
    private const PRODUCT_CODE = '0003';
    private const STOCK = 2;

    private ProductAvailabilityCreateStepExecutor $executor;

    private ProductAvailabilityServiceInterface $productAvailabilityService;

    private ProductServiceInterface $productService;

    protected function setUp(): void
    {
        self::setAdministratorUser();

        $this->productAvailabilityService = self::getServiceByClassName(ProductAvailabilityServiceInterface::class);
        $this->productService = self::getServiceByClassName(ProductServiceInterface::class);

        $this->executor = new ProductAvailabilityCreateStepExecutor(
            $this->productAvailabilityService,
            $this->productService
        );
        $this->configureExecutor($this->executor);
    }

    public function testHandle(): void
    {
        $product = $this->productService->getProduct(self::PRODUCT_CODE);
        try {
            $this->productAvailabilityService->getAvailability($product);
            self::fail(sprintf('Product availability for product with code %s already exists', self::PRODUCT_CODE));
        } catch (NotFoundException $e) {
            // Expected
        }

        self::assertFalse($this->executor->canHandle($this->createMock(StepInterface::class)));

        $step = new ProductAvailabilityCreateStep(
            self::PRODUCT_CODE,
            self::STOCK,
            true,
            false,
        );

        self::assertTrue($this->executor->canHandle($step));
        $this->executor->handle($step);

        $availability = $this->productAvailabilityService->getAvailability($product);
        self::assertSame(self::PRODUCT_CODE, $availability->getProduct()->getCode());
        self::assertTrue($availability->isAvailable());
        self::assertFalse($availability->isInfinite());
        self::assertSame(self::STOCK, $availability->getStock());
    }
}
