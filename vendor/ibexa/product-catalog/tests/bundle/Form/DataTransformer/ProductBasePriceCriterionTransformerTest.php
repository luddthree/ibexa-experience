<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Form\DataTransformer;

use Ibexa\Bundle\ProductCatalog\Form\Data\Catalog\CatalogFilterPriceData;
use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\ProductBasePriceCriterionTransformer;
use Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\BasePriceRange;
use Ibexa\ProductCatalog\Local\Repository\Values\CurrencyList;
use Ibexa\ProductCatalog\Money\DecimalMoneyFactory;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @covers \Ibexa\Bundle\ProductCatalog\Form\DataTransformer\ProductBasePriceCriterionTransformer
 */
final class ProductBasePriceCriterionTransformerTest extends TestCase
{
    private const EXAMPLE_CURRENCY_CODE = 'USD';
    private const EXAMPLE_CURRENCY_AMOUNT_MIN = 101;
    private const EXAMPLE_CURRENCY_AMOUNT_MAX = 202;

    private ProductBasePriceCriterionTransformer $transformer;

    protected function setUp(): void
    {
        $currencyService = $this->createMock(CurrencyServiceInterface::class);
        $currencyList = new CurrencyList(
            [
                new \Ibexa\ProductCatalog\Local\Repository\Values\Currency(
                    1,
                    self::EXAMPLE_CURRENCY_CODE,
                    2,
                    true
                ),
            ],
            1
        );

        $currencyService
            ->method('findCurrencies')
            ->willReturn($currencyList);

        $this->transformer = new ProductBasePriceCriterionTransformer(new DecimalMoneyFactory($currencyService));
    }

    /**
     * @dataProvider dataProviderForTestTransformDataProvider
     */
    public function testTransform(?BasePriceRange $value, ?CatalogFilterPriceData $expected): void
    {
        self::assertEquals($expected, $this->transformer->transform($value));
    }

    /**
     * @return iterable<string,mixed>
     */
    public function dataProviderForTestTransformDataProvider(): iterable
    {
        $moneyMin = new Money(
            self::EXAMPLE_CURRENCY_AMOUNT_MIN,
            new Currency(self::EXAMPLE_CURRENCY_CODE)
        );
        $moneyMax = new Money(
            self::EXAMPLE_CURRENCY_AMOUNT_MAX,
            new Currency(self::EXAMPLE_CURRENCY_CODE)
        );
        $currency = new Currency(self::EXAMPLE_CURRENCY_CODE);

        yield 'null' => [null, null];
        yield 'range' => [
            new BasePriceRange($moneyMin, $moneyMax),
            new CatalogFilterPriceData(
                $currency,
                1.01,
                2.02
            ),
        ];
        yield 'min' => [
            new BasePriceRange($moneyMin, null),
            new CatalogFilterPriceData(
                $currency,
                1.01,
                null
            ),
        ];
        yield 'max' => [
            new BasePriceRange(null, $moneyMax),
            new CatalogFilterPriceData(
                $currency,
                null,
                2.02
            ),
        ];
    }

    public function testTransformWithInvalidInput(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage(
            'Expected a ' . BasePriceRange::class . ' object, received ' . stdClass::class . '.'
        );

        $this->transformer->transform(new stdClass());
    }

    /**
     * @dataProvider dataProviderForTestReverseTransformDataProvider
     */
    public function testReverseTransform(?CatalogFilterPriceData $value, ?BasePriceRange $expected): void
    {
        self::assertEquals($expected, $this->transformer->reverseTransform($value));
    }

    /**
     * @return iterable<string,mixed>
     */
    public function dataProviderForTestReverseTransformDataProvider(): iterable
    {
        $moneyMin = new Money(
            self::EXAMPLE_CURRENCY_AMOUNT_MIN,
            new Currency(self::EXAMPLE_CURRENCY_CODE)
        );
        $moneyMax = new Money(
            self::EXAMPLE_CURRENCY_AMOUNT_MAX,
            new Currency(self::EXAMPLE_CURRENCY_CODE)
        );
        $currency = new Currency(self::EXAMPLE_CURRENCY_CODE);

        yield 'null' => [
            new CatalogFilterPriceData(),
            null,
        ];
        yield 'range' => [
            new CatalogFilterPriceData(
                $currency,
                1.01,
                2.02
            ),
            new BasePriceRange($moneyMin, $moneyMax),
        ];
        yield 'min' => [
            new CatalogFilterPriceData(
                $currency,
                1.01,
                null
            ),
            new BasePriceRange($moneyMin, null),
        ];
        yield 'max' => [
            new CatalogFilterPriceData(
                $currency,
                null,
                2.02
            ),
            new BasePriceRange(null, $moneyMax),
        ];
    }

    /**
     * @dataProvider dataProviderForTestReverseTransformWithInvalidInputDataProvider
     *
     * @param mixed $value
     */
    public function testReverseTransformWithInvalidInput($value): void
    {
        $this->expectException(TransformationFailedException::class);

        $this->transformer->reverseTransform($value);
    }

    /**
     * @return iterable<string,array{mixed}>
     */
    public function dataProviderForTestReverseTransformWithInvalidInputDataProvider(): iterable
    {
        yield 'string' => ['foo'];
        yield 'bool' => [true];
        yield 'float' => [12.34];
        yield 'array' => [['element']];
        yield 'object' => [new stdClass()];
    }
}
