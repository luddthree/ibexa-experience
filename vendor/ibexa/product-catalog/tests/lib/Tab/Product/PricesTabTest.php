<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Tab\Product;

use ArrayIterator;
use EmptyIterator;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface;
use Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\ProductPriceServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyListInterface;
use Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyQuery;
use Ibexa\Contracts\ProductCatalog\Values\Currency\Query\Criterion\IsCurrencyEnabledCriterion;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\PriceListInterface;
use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\ProductCatalog\Tab\Product\PricesTab;
use Pagerfanta\Pagerfanta;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class PricesTabTest extends TestCase
{
    private const EXAMPLE_PRODUCT_CODE = '0001';
    private const EXAMPLE_CURRENCY_CODE = 'EUR';

    /** @var \Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private CurrencyServiceInterface $currencyService;

    /** @var \Ibexa\Contracts\ProductCatalog\ProductPriceServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ProductPriceServiceInterface $priceService;

    private PricesTab $pricesTab;

    protected function setUp(): void
    {
        $this->currencyService = $this->createMock(CurrencyServiceInterface::class);
        $this->priceService = $this->createMock(ProductPriceServiceInterface::class);

        $configResolver = $this->createMock(ConfigResolverInterface::class);
        $configResolver
            ->method('getParameter')
            ->with('product_catalog.pagination.product_view_custom_prices_limit')
            ->willReturn(3);

        $this->pricesTab = new PricesTab(
            $this->createMock(Environment::class),
            $this->createMock(TranslatorInterface::class),
            $this->createMock(EventDispatcherInterface::class),
            $this->currencyService,
            $this->priceService,
            $this->createMock(CustomerGroupServiceInterface::class),
            $this->createFormFactoryMock(),
            $configResolver,
            $this->createMock(RequestStack::class),
        );
    }

    public function testGetTemplateParameters(): void
    {
        $product = $this->getExampleProduct();
        $currency = $this->getExampleCurrency();

        $expectedMainPrice = $this->createMock(PriceInterface::class);

        $this->currencyService
            ->method('getCurrencyByCode')
            ->with(self::EXAMPLE_CURRENCY_CODE)
            ->willReturn($currency);

        $this->priceService
            ->method('getPriceByProductAndCurrency')
            ->with($product, $currency)
            ->willReturn($expectedMainPrice);

        $actualParameters = $this->pricesTab->getTemplateParameters([
            'product' => $product,
            'currency' => self::EXAMPLE_CURRENCY_CODE,
        ]);

        self::assertSame($currency, $actualParameters['currency']);
        self::assertInstanceOf(FormView::class, $actualParameters['currency_choice_form']);
        self::assertSame($expectedMainPrice, $actualParameters['main_price']);
        self::assertInstanceOf(Pagerfanta::class, $actualParameters['custom_price_items']);
        self::assertFalse($actualParameters['no_currencies']);
    }

    public function testGetTemplateParametersForMissingAtLeastOneEnabledCurrencies(): void
    {
        $product = $this->getExampleProduct();

        $this->configureEnabledCurrencies(true);
        $this->configureEmptyPriceListForProduct();

        $actualParameters = $this->pricesTab->getTemplateParameters([
            'product' => $product,
        ]);

        self::assertNull($actualParameters['currency']);
        self::assertTrue($actualParameters['no_currencies']);
    }

    public function testGetTemplateParametersWithMissingProductPricing(): void
    {
        $product = $this->getExampleProduct();

        $this->configureEmptyPriceListForProduct();
        $expectedCurrency = $this->configureEnabledCurrencies();

        $actualParameters = $this->pricesTab->getTemplateParameters([
            'product' => $product,
        ]);

        self::assertSame($expectedCurrency, $actualParameters['currency']);
        self::assertInstanceOf(FormView::class, $actualParameters['currency_choice_form']);
        self::assertFalse($actualParameters['no_currencies']);
    }

    private function createFormFactoryMock(): FormFactoryInterface
    {
        $form = $this->createMock(FormInterface::class);
        $form->method('createView')->willReturn($this->createMock(FormView::class));

        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->method('createNamed')->willReturn($form);

        return $formFactory;
    }

    private function getExampleCurrency(): CurrencyInterface
    {
        $currency = $this->createMock(CurrencyInterface::class);
        $currency->method('getCode')->willReturn(self::EXAMPLE_PRODUCT_CODE);
        $currency->method('isEnabled')->willReturn(true);

        return $currency;
    }

    private function getExampleProduct(): ProductInterface
    {
        $product = $this->createMock(ProductInterface::class);
        $product->method('getCode')->willReturn(self::EXAMPLE_PRODUCT_CODE);

        return $product;
    }

    private function configureEmptyPriceListForProduct(): void
    {
        $priceLists = $this->createMock(PriceListInterface::class);
        $priceLists->method('getIterator')->willReturn(new EmptyIterator());

        $this->priceService
            ->method('findPricesByProductCode')
            ->with(self::EXAMPLE_PRODUCT_CODE)
            ->willReturn($priceLists);
    }

    private function configureEnabledCurrencies(bool $empty = false): ?CurrencyInterface
    {
        $currency = null;

        $expectedQuery = new CurrencyQuery(new IsCurrencyEnabledCriterion());
        $expectedQuery->setLimit(1);

        $currenciesList = $this->createMock(CurrencyListInterface::class);

        if ($empty) {
            $currenciesList->method('getTotalCount')->willReturn(0);
            $currenciesList->method('getIterator')->willReturn(new EmptyIterator());
        } else {
            $currency = $this->createMock(CurrencyInterface::class);

            $currenciesList->method('getTotalCount')->willReturn(1);
            $currenciesList->method('getIterator')->willReturn(new ArrayIterator([$currency]));
        }

        $currenciesList->method('getTotalCount')->willReturn($empty ? 0 : 1);
        $currenciesList->method('getIterator')->willReturn(
            $empty ? new EmptyIterator() : new ArrayIterator([$this->createMock(CurrencyInterface::class)])
        );

        $this->currencyService
            ->method('findCurrencies')
            ->with($expectedQuery)
            ->willReturn($currenciesList);

        return $currency;
    }
}
