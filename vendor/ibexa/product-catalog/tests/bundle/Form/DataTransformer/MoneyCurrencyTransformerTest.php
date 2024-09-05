<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Form\DataTransformer;

use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\MoneyCurrencyTransformer;
use Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Core\Base\Exceptions\NotFoundException as CoreNotFoundException;
use Ibexa\ProductCatalog\Local\Persistence\Values\Currency as PersistenceCurrency;
use Money\Currency as MoneyCurrency;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class MoneyCurrencyTransformerTest extends TestCase
{
    private const EXAMPLE_CURRENCY_CODE = 'USD';
    private const MISSING_CURRENCY_CODE = 'FOO';

    /** @var \Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private CurrencyServiceInterface $currencyService;

    private MoneyCurrencyTransformer $transformer;

    /** @var \Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface|\PHPUnit\Framework\MockObject\MockObject */
    private CurrencyInterface $currency;

    protected function setUp(): void
    {
        $this->currency = $this->createMock(CurrencyInterface::class);
        $this->currency->method('getCode')->willReturn(self::EXAMPLE_CURRENCY_CODE);

        $this->currencyService = $this->createMock(CurrencyServiceInterface::class);
        $this->currencyService
            ->method('getCurrencyByCode')
            ->with(self::EXAMPLE_CURRENCY_CODE)
            ->willReturn($this->currency);

        $this->transformer = new MoneyCurrencyTransformer($this->currencyService);
    }

    /**
     * @dataProvider dataProviderForTestTransformDataProvider
     */
    public function testTransform(?MoneyCurrency $value, ?CurrencyInterface $expected): void
    {
        self::assertEquals($expected, $this->transformer->transform($value));
    }

    /**
     * @return iterable<string,mixed>
     */
    public function dataProviderForTestTransformDataProvider(): iterable
    {
        $moneyCurrency = new MoneyCurrency(self::EXAMPLE_CURRENCY_CODE);

        $currency = $this->createMock(CurrencyInterface::class);
        $currency->method('getCode')->willReturn(self::EXAMPLE_CURRENCY_CODE);

        yield 'null' => [null, null];
        yield 'currency' => [$moneyCurrency, $currency];
    }

    public function testTransformWithInvalidInput(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected a ' . MoneyCurrency::class . ' object.');

        $this->transformer->transform(new stdClass());
    }

    public function testTransformWithMissingCurrency(): void
    {
        $this->currencyService
            ->method('getCurrencyByCode')
            ->with(self::MISSING_CURRENCY_CODE)
            ->willThrowException(
                new CoreNotFoundException(PersistenceCurrency::class, self::MISSING_CURRENCY_CODE)
            );

        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage(
            sprintf(
                "Could not find '%s' with identifier '%s'",
                PersistenceCurrency::class,
                self::MISSING_CURRENCY_CODE
            )
        );

        $this->transformer->transform(new MoneyCurrency(self::MISSING_CURRENCY_CODE));
    }

    public function testReverseTransform(): void
    {
        self::assertEquals(
            new MoneyCurrency(self::EXAMPLE_CURRENCY_CODE),
            $this->transformer->reverseTransform($this->currency)
        );
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
        yield 'integer' => [123456];
        yield 'bool' => [true];
        yield 'float' => [12.34];
        yield 'array' => [['element']];
        yield 'object' => [new stdClass()];
    }
}
