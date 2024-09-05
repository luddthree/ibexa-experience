<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Form\DataTransformer;

use Exception;
use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\CurrencyTransformer;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class CurrencyTransformerTest extends TestCase
{
    private const EXAMPLE_CURRENCY_CODE = 'USD';

    /** @var \Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private CurrencyServiceInterface $currencyService;

    private CurrencyTransformer $transformer;

    protected function setUp(): void
    {
        $this->currencyService = $this->createMock(CurrencyServiceInterface::class);
        $this->transformer = new CurrencyTransformer($this->currencyService);
    }

    /**
     * @dataProvider dataProviderForTestTransformDataProvider
     */
    public function testTransform(?CurrencyInterface $value, ?string $expected): void
    {
        self::assertEquals($expected, $this->transformer->transform($value));
    }

    /**
     * @return iterable<string,mixed>
     */
    public function dataProviderForTestTransformDataProvider(): iterable
    {
        $currency = $this->createMock(CurrencyInterface::class);
        $currency->method('getCode')->willReturn(self::EXAMPLE_CURRENCY_CODE);

        yield 'null' => [null, null];
        yield 'currency' => [$currency, self::EXAMPLE_CURRENCY_CODE];
    }

    public function testTransformWithInvalidInput(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected a ' . CurrencyInterface::class . ' object.');

        $this->transformer->transform(new stdClass());
    }

    public function testReverseTransform(): void
    {
        $currency = $this->createMock(CurrencyInterface::class);

        $this->currencyService
            ->method('getCurrencyByCode')
            ->with(self::EXAMPLE_CURRENCY_CODE)
            ->willReturn($currency);

        self::assertEquals(
            $currency,
            $this->transformer->reverseTransform(self::EXAMPLE_CURRENCY_CODE)
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

    /**
     * @dataProvider dataProviderForTestReverseTransformHandleCurrencyLoadFailure
     */
    public function testReverseTransformHandleCurrencyLoadFailure(Exception $exception): void
    {
        $this->expectException(TransformationFailedException::class);

        $this->currencyService
            ->method('getCurrencyByCode')
            ->with(self::EXAMPLE_CURRENCY_CODE)
            ->willThrowException($exception);

        $this->transformer->reverseTransform(self::EXAMPLE_CURRENCY_CODE);
    }

    /**
     * @return iterable<string,mixed>
     */
    public function dataProviderForTestReverseTransformHandleCurrencyLoadFailure(): iterable
    {
        yield NotFoundException::class => [
            $this->createMock(NotFoundException::class),
        ];
    }
}
