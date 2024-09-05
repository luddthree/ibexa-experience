<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Migrations\ProductPrice;

use Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface;
use Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\ProductPriceServiceInterface;
use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomPriceAwareInterface;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\ProductCatalog\Migrations\ProductPrice\ProductCustomPrice;
use Ibexa\ProductCatalog\Migrations\ProductPrice\ProductPriceCreateStep;
use Ibexa\ProductCatalog\Migrations\ProductPrice\ProductPriceCreateStepExecutor;
use Ibexa\Tests\Integration\ProductCatalog\Migrations\AbstractStepExecutorTest;
use Money\Currency;
use Money\Money;

/**
 * @covers \Ibexa\ProductCatalog\Migrations\ProductPrice\ProductPriceCreateStepExecutor
 */
final class ProductPriceCreateStepExecutorTest extends AbstractStepExecutorTest
{
    private const EXAMPLE_PRODUCT_CODE = '0003';
    private const EXAMPLE_CURRENCY_CODE = 'EUR';

    private ProductServiceInterface $productService;

    private CurrencyServiceInterface $currencyService;

    private CustomerGroupServiceInterface $customerGroupService;

    private ProductPriceServiceInterface $priceService;

    private ProductPriceCreateStepExecutor $executor;

    protected function setUp(): void
    {
        self::setAdministratorUser();

        $this->productService = self::getServiceByClassName(ProductServiceInterface::class);
        $this->currencyService = self::getServiceByClassName(CurrencyServiceInterface::class);
        $this->priceService = self::getServiceByClassName(ProductPriceServiceInterface::class);
        $this->customerGroupService = self::getServiceByClassName(CustomerGroupServiceInterface::class);

        $this->executor = new ProductPriceCreateStepExecutor(
            $this->productService,
            $this->currencyService,
            $this->customerGroupService,
            $this->priceService
        );

        $this->configureExecutor($this->executor);
    }

    public function testCanHandle(): void
    {
        self::assertFalse($this->executor->canHandle($this->createMock(StepInterface::class)));

        self::assertTrue($this->executor->canHandle(
            new ProductPriceCreateStep(
                self::EXAMPLE_PRODUCT_CODE,
                new Money(10000, new Currency(self::EXAMPLE_CURRENCY_CODE)),
                self::EXAMPLE_CURRENCY_CODE
            )
        ));
    }

    public function testHandle(): void
    {
        $step = new ProductPriceCreateStep(
            self::EXAMPLE_PRODUCT_CODE,
            new Money(10000, new Currency(self::EXAMPLE_CURRENCY_CODE)),
            self::EXAMPLE_CURRENCY_CODE,
            [
                new ProductCustomPrice(
                    'customer_group_1',
                    new Money(12000, new Currency(self::EXAMPLE_CURRENCY_CODE)),
                    new Money(9000, new Currency(self::EXAMPLE_CURRENCY_CODE)),
                ),
                new ProductCustomPrice(
                    'customer_group_2',
                    null,
                    new Money(8000, new Currency(self::EXAMPLE_CURRENCY_CODE)),
                ),
            ]
        );

        $this->executor->handle($step);

        $price = $this->priceService->getPriceByProductAndCurrency(
            $this->productService->getProduct(self::EXAMPLE_PRODUCT_CODE),
            $this->currencyService->getCurrencyByCode(self::EXAMPLE_CURRENCY_CODE)
        );

        self::assertEquals(self::EXAMPLE_PRODUCT_CODE, $price->getProduct()->getCode());
        self::assertEquals(self::EXAMPLE_CURRENCY_CODE, $price->getCurrency()->getCode());
        self::assertEquals(100.00, $price->getAmount());

        self::assertCustomPrice(
            $this->priceService->findOneForCustomerGroup(
                $price,
                $this->customerGroupService->getCustomerGroupByIdentifier('customer_group_1')
            ),
            self::EXAMPLE_PRODUCT_CODE,
            self::EXAMPLE_CURRENCY_CODE,
            '120.00',
            '90.00'
        );

        self::assertCustomPrice(
            $this->priceService->findOneForCustomerGroup(
                $price,
                $this->customerGroupService->getCustomerGroupByIdentifier('customer_group_2')
            ),
            self::EXAMPLE_PRODUCT_CODE,
            self::EXAMPLE_CURRENCY_CODE,
            '100.00',
            '80.00'
        );
    }

    /**
     * @param numeric-string $expectedBaseAmount
     * @param numeric-string $expectedCustomAmount
     */
    private static function assertCustomPrice(
        ?CustomPriceAwareInterface $customPrice,
        string $expectedProductCode,
        string $expectedCurrencyCode,
        string $expectedBaseAmount,
        string $expectedCustomAmount
    ): void {
        self::assertNotNull($customPrice);
        self::assertSame($expectedProductCode, $customPrice->getProduct()->getCode());
        self::assertSame($expectedCurrencyCode, $customPrice->getCurrency()->getCode());
        self::assertSame($expectedBaseAmount, $customPrice->getBaseAmount());
        self::assertSame($expectedCustomAmount, $customPrice->getCustomPriceAmount());
    }
}
