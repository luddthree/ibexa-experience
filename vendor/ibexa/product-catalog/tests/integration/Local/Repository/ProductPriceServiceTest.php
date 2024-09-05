<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\Create\Struct\CustomerGroupPriceCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Price\Create\Struct\ProductPriceCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Price\Delete\Struct\ProductPriceDeleteStruct;
use Ibexa\Contracts\ProductCatalog\Values\Price\PriceListInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\PriceQuery;
use Ibexa\Contracts\ProductCatalog\Values\Price\Query\Criterion;
use Ibexa\Contracts\ProductCatalog\Values\Price\Update\Struct\ProductPriceUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\Price;
use Ibexa\ProductCatalog\Local\Repository\Values\Price\CustomGroupPrice;
use Ibexa\Tests\Integration\ProductCatalog\Fixtures\CurrencyFixture;
use Ibexa\Tests\Integration\ProductCatalog\Fixtures\CustomerGroupFixture;
use Money\Currency;
use Money\Money;
use Symfony\Bridge\PhpUnit\ClockMock;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Exception\ValidationFailedException;

/**
 * @covers \Ibexa\ProductCatalog\Local\Repository\ProductPriceService
 *
 * @group product-price-service
 *
 * @phpstan-type TQueryFactory callable(): PriceQuery
 */
final class ProductPriceServiceTest extends BaseProductPriceServiceTest
{
    /**
     * @dataProvider dataProviderForTestFindPrices
     *
     * @param \Ibexa\Contracts\ProductCatalog\Values\Price\PriceQuery|callable(): \Ibexa\Contracts\ProductCatalog\Values\Price\PriceQuery|null $query
     */
    public function testFindPrices($query, int $expectedTotalCount): void
    {
        if (is_callable($query)) {
            $query = $query();
        }

        $customerGroup = $this->getExampleCustomerGroup();
        $this->createCustomerGroupProductPrice($customerGroup);

        $this->assertPriceList(
            $this->productPriceService->findPrices($query),
            $expectedTotalCount
        );
    }

    /**
     * @return iterable<string, array{PriceQuery|TQueryFactory|null, int}>
     */
    public function dataProviderForTestFindPrices(): iterable
    {
        yield 'empty' => [
            null,
            11,
        ];

        yield 'find by product' => [
            new PriceQuery(new Criterion\Product(self::EXAMPLE_PRODUCT_CODE)),
            6,
        ];

        yield 'find by currency' => [
            static fn (): PriceQuery => new PriceQuery(new Criterion\Currency(
                self::getCurrencyService()->getCurrencyByCode('EUR')
            )),
            6,
        ];

        yield 'find by customer group' => [
            fn (): PriceQuery => new PriceQuery(new Criterion\CustomerGroup(
                $this->getExampleCustomerGroup()
            )),
            1,
        ];
    }

    public function testFindPricesByProductCode(): void
    {
        $result = $this->productPriceService->findPricesByProductCode(self::EXAMPLE_PRODUCT_CODE);

        $count = count(CurrencyFixture::CURRENCY_IDS);
        self::assertCount($count, $result);
        self::assertSame($count, $result->getTotalCount());
        self::assertContainsOnlyInstancesOf(PriceInterface::class, $result);
        self::assertContainsOnlyInstancesOf(PriceInterface::class, $result->getPrices());
    }

    public function testUpdateProductPrice(): void
    {
        $price = $this->productPriceService->getPriceById(5);
        $product = $price->getProduct();

        self::assertSame('42.00', $price->getAmount());

        $struct = new ProductPriceUpdateStruct(
            $price,
            new Money('6600', new Currency('EUR')),
        );

        $this->sleep();
        $price = $this->productPriceService->updateProductPrice($struct);

        self::assertInstanceOf(Price::class, $price);
        self::assertSame('66.00', $price->getAmount());

        $this->assertProductIsUpdated($product);
    }

    public function testPartiallyUpdateProductPrice(): void
    {
        $price = $this->productPriceService->getPriceById(5);
        $product = $price->getProduct();

        self::assertSame('42.00', $price->getAmount());

        $struct = new ProductPriceUpdateStruct($price);

        $this->sleep();
        $price = $this->productPriceService->updateProductPrice($struct);

        self::assertInstanceOf(Price::class, $price);
        self::assertSame('42.00', $price->getAmount());

        $this->assertProductIsUpdated($product);
    }

    public function testPartiallyUpdateCustomerGroupProductPrice(): void
    {
        $price = $this->productPriceService->getPriceById(6);
        $product = $price->getProduct();

        self::assertInstanceOf(CustomGroupPrice::class, $price);
        self::assertSame('42.00', $price->getAmount());

        $struct = new ProductPriceUpdateStruct($price);

        $this->sleep();
        $price = $this->productPriceService->updateProductPrice($struct);

        self::assertInstanceOf(CustomGroupPrice::class, $price);
        self::assertSame('42.00', $price->getAmount());

        $this->assertProductIsUpdated($product);
    }

    public function testCreateProductPrice(): void
    {
        $product = self::getLocalProductService()->getProduct(self::EXAMPLE_PRODUCT_CODE2);
        $eurCurrency = $this->getKnownExistingEurCurrencyMock();

        $struct = new ProductPriceCreateStruct(
            $product,
            $eurCurrency,
            new Money('6600', new Currency('EUR')),
            null,
            null
        );

        $price = $this->productPriceService->createProductPrice($struct);

        self::assertInstanceOf(Price::class, $price);
        self::assertSame('66.00', $price->getAmount());

        $this->assertProductIsUpdated($product);

        $usdCurrency = $this->getKnownExistingUsdCurrencyMock();

        $struct = new ProductPriceCreateStruct(
            $product,
            $usdCurrency,
            new Money('77777', new Currency('USD')),
            null,
            null
        );

        $price = $this->productPriceService->createProductPrice($struct);

        self::assertInstanceOf(Price::class, $price);
        self::assertSame('77.777', $price->getAmount());

        $this->assertProductIsUpdated($product);

        $prices = $this->productPriceService->findPricesByProductCode($product->getCode());
        self::assertCount(2, $prices);

        $this->assertProductIsUpdated($product);

        $struct = new ProductPriceCreateStruct(
            $product,
            $eurCurrency,
            new Money('120000', new Currency('EUR')),
            null,
            null
        );

        try {
            $this->productPriceService->createProductPrice($struct);
            self::fail('Validation should have failed');
        } catch (ValidationFailedException $e) {
            $violations = $e->getViolations();
            self::assertCount(1, $violations);

            $violation = $violations[0];
            self::assertInstanceOf(ConstraintViolation::class, $violation);
            self::assertSame(
                'Product price already exists for product with code 0001 for currency EUR',
                $violation->getMessage(),
            );
            self::assertSame([
                '%product_code%' => '0001',
                '%currency_code%' => 'EUR',
            ], $violation->getParameters());
        }

        $prices = $this->productPriceService->findPricesByProductCode($product->getCode());
        self::assertCount(2, $prices);
    }

    public function testCreateCustomerGroupProductPrice(): void
    {
        $product = self::getLocalProductService()->getProduct(self::EXAMPLE_PRODUCT_CODE2);
        $eurCurrency = $this->getKnownExistingEurCurrencyMock();
        $usdCurrency = $this->getKnownExistingUsdCurrencyMock();
        $customerGroup = $this->getKnownExistingCustomerGroupMock();
        $customerGroupTwo = $this->getAnotherKnownExistingCustomerGroupMock();

        $struct = new CustomerGroupPriceCreateStruct(
            $customerGroup,
            $product,
            $eurCurrency,
            new Money('6600', new Currency('EUR')),
            null,
            null
        );

        $price = $this->productPriceService->createProductPrice($struct);

        self::assertInstanceOf(CustomGroupPrice::class, $price);
        self::assertSame('66.00', $price->getAmount());
        $this->assertProductIsUpdated($product);

        // Allow creating a Product Price for different Customer Group
        $struct = new CustomerGroupPriceCreateStruct(
            $customerGroupTwo,
            $product,
            $eurCurrency,
            new Money('120000', new Currency('EUR')),
            null,
            null
        );
        $price = $this->productPriceService->createProductPrice($struct);

        self::assertInstanceOf(CustomGroupPrice::class, $price);
        self::assertSame('1200.00', $price->getAmount());
        $this->assertProductIsUpdated($product);

        // Allow creating a Product Price for different Currency
        $struct = new CustomerGroupPriceCreateStruct(
            $customerGroup,
            $product,
            $usdCurrency,
            new Money('77777', new Currency('USD')),
            null,
            null
        );
        $price = $this->productPriceService->createProductPrice($struct);

        self::assertInstanceOf(CustomGroupPrice::class, $price);
        self::assertSame('77.777', $price->getAmount());
        $this->assertProductIsUpdated($product);

        $prices = $this->productPriceService->findPricesByProductCode($product->getCode());
        self::assertCount(0, $prices);

        try {
            $this->productPriceService->createProductPrice($struct);
            self::fail('Validation should have failed');
        } catch (ValidationFailedException $e) {
            $violations = $e->getViolations();
            self::assertCount(1, $violations);

            $violation = $violations[0];
            self::assertInstanceOf(ConstraintViolation::class, $violation);
            self::assertSame(
                'Product price already exists for customer group answer to everything, product with code 0001, for currency USD',
                $violation->getMessage(),
            );
            self::assertSame([
                '%customer_group_identifier%' => 'answer to everything',
                '%product_code%' => '0001',
                '%currency_code%' => 'USD',
            ], $violation->getParameters());
        }
    }

    public function testFindPriceById(): void
    {
        $price = $this->productPriceService->getPriceById(5);
        self::assertSame(5, $price->getId());
        self::assertSame('42.00', $price->getAmount());
        self::assertInstanceOf(Price::class, $price);

        $customerGroupPrice = $this->productPriceService->getPriceById(6);
        self::assertInstanceOf(CustomGroupPrice::class, $customerGroupPrice);
        self::assertSame(6, $customerGroupPrice->getId());
    }

    public function testFindPriceByProductAndCurrency(): void
    {
        $product = $this->getKnownExistingProductMock();
        $currency = $this->getKnownExistingEurCurrencyMock();

        $price = $this->productPriceService->getPriceByProductAndCurrency($product, $currency);

        self::assertSame(5, $price->getId());
        self::assertSame(self::EXAMPLE_PRODUCT_CODE, $price->getProduct()->getCode());
        self::assertSame(CurrencyFixture::EUR_ID, $price->getCurrency()->getId());
        self::assertSame('42.00', $price->getAmount());
    }

    public function testFindPriceByProductAndCurrencyThrowsNotFoundException(): void
    {
        $product = self::getLocalProductService()->getProduct(self::EXAMPLE_PRODUCT_CODE2);
        $currency = self::getCurrencyService()->createCurrency(new CurrencyCreateStruct('XXX', 2, true));

        $this->expectException(NotFoundException::class);

        $this->productPriceService->getPriceByProductAndCurrency($product, $currency);
    }

    public function testPriceDelete(): void
    {
        $price = $this->productPriceService->getPriceById(5);
        $struct = new ProductPriceDeleteStruct($price);
        $product = $price->getProduct();

        $this->sleep();
        $this->productPriceService->deletePrice($struct);
        $this->assertProductIsUpdated($product);

        $this->expectException(NotFoundException::class);
        $this->productPriceService->getPriceById(5);
    }

    private function getKnownExistingCustomerGroupMock(): CustomerGroupInterface
    {
        $customerGroup = $this->createMock(CustomerGroupInterface::class);
        $customerGroup
            ->expects(self::atLeastOnce())
            ->method('getId')
            ->willReturn(CustomerGroupFixture::FIXTURE_ENTRY_ID);

        return $customerGroup;
    }

    private function getAnotherKnownExistingCustomerGroupMock(): CustomerGroupInterface
    {
        $customerGroup = $this->createMock(CustomerGroupInterface::class);
        $customerGroup
            ->expects(self::atLeastOnce())
            ->method('getId')
            ->willReturn(CustomerGroupFixture::FIXTURE_SECOND_ENTRY_ID);

        return $customerGroup;
    }

    private function getKnownExistingEurCurrencyMock(): CurrencyInterface
    {
        $currency = $this->createMock(CurrencyInterface::class);
        $currency
            ->expects(self::atLeastOnce())
            ->method('getId')
            ->willReturn(CurrencyFixture::EUR_ID);

        return $currency;
    }

    private function getKnownExistingUsdCurrencyMock(): CurrencyInterface
    {
        $currency = $this->createMock(CurrencyInterface::class);
        $currency
            ->expects(self::atLeastOnce())
            ->method('getId')
            ->willReturn(CurrencyFixture::USD_ID);

        return $currency;
    }

    private function getKnownExistingProductMock(): ProductInterface
    {
        $product = $this->createMock(ProductInterface::class);
        $product
            ->expects(self::atLeastOnce())
            ->method('getCode')
            ->willReturn(self::EXAMPLE_PRODUCT_CODE);

        return $product;
    }

    private function assertProductIsUpdated(ProductInterface $productBeforePriceChange): void
    {
        $productAfterPriceChange = self::getProductService()->getProduct(
            $productBeforePriceChange->getCode()
        );

        self::assertGreaterThan(
            $productBeforePriceChange->getUpdatedAt()->getTimestamp(),
            $productAfterPriceChange->getUpdatedAt()->getTimestamp()
        );
    }

    private function assertPriceList(PriceListInterface $priceList, int $expectedTotalCount): void
    {
        $actualPricesIds = [];
        /** @var \Ibexa\Contracts\ProductCatalog\Values\PriceInterface $price */
        foreach ($priceList as $price) {
            $actualPricesIds[] = $price->getId();
        }

        self::assertEquals($expectedTotalCount, $priceList->getTotalCount());
    }

    private function sleep(): void
    {
        ClockMock::sleep(1);
    }

    private function getExampleCustomerGroup(): CustomerGroupInterface
    {
        return self::getCustomerGroupService()->getCustomerGroupByIdentifier('customer_group_1');
    }

    private function createCustomerGroupProductPrice(CustomerGroupInterface $customerGroup): void
    {
        $product = self::getLocalProductService()->getProduct(self::EXAMPLE_PRODUCT_CODE2);
        $currency = $this->getKnownExistingEurCurrencyMock();

        $struct = new CustomerGroupPriceCreateStruct(
            $customerGroup,
            $product,
            $currency,
            new Money('6600', new Currency('EUR')),
            null,
            null
        );

        $this->productPriceService->createProductPrice($struct);
    }
}
