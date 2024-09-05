<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Form\DataTransformer;

use Exception;
use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\ProductPriceTransformer;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\ProductCatalog\ProductPriceServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @covers \Ibexa\Bundle\ProductCatalog\Form\DataTransformer\ProductPriceTransformer
 */
final class ProductPriceTransformerTest extends TestCase
{
    private const EXAMPLE_PRICE_ID = 66;

    /** @var \Ibexa\Contracts\ProductCatalog\ProductPriceServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ProductPriceServiceInterface $productPriceService;

    private ProductPriceTransformer $transformer;

    protected function setUp(): void
    {
        $this->productPriceService = $this->createMock(ProductPriceServiceInterface::class);
        $this->transformer = new ProductPriceTransformer($this->productPriceService);
    }

    /**
     * @dataProvider dataProviderForTestTransformDataProvider
     */
    public function testTransform(?PriceInterface $value, ?int $expected): void
    {
        self::assertEquals($expected, $this->transformer->transform($value));
    }

    /**
     * @return iterable<string, array{?\Ibexa\Contracts\ProductCatalog\Values\PriceInterface, ?int}>
     */
    public function dataProviderForTestTransformDataProvider(): iterable
    {
        $price = $this->createMock(PriceInterface::class);
        $price->method('getId')->willReturn(self::EXAMPLE_PRICE_ID);

        yield 'null' => [null, null];
        yield 'Price' => [$price, self::EXAMPLE_PRICE_ID];
    }

    public function testTransformWithInvalidInput(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected a ' . PriceInterface::class . ' object.');

        $this->transformer->transform(new stdClass());
    }

    public function testReverseTransform(): void
    {
        $price = $this->createMock(PriceInterface::class);

        $this->productPriceService
            ->method('getPriceById')
            ->with(self::EXAMPLE_PRICE_ID)
            ->willReturn($price);

        self::assertEquals($price, $this->transformer->reverseTransform(self::EXAMPLE_PRICE_ID));
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
     * @return iterable<string,array{scalar|array|object}>
     */
    public function dataProviderForTestReverseTransformWithInvalidInputDataProvider(): iterable
    {
        yield 'string' => ['foo'];
        yield 'bool' => [true];
        yield 'float' => [12.34];
        yield 'array' => [['element']];
        yield 'object' => [new stdClass()];
    }

    /**
     * @dataProvider dataProviderForTestReverseTransformHandleProductLoadFailure
     */
    public function testReverseTransformHandleProductLoadFailure(Exception $exception): void
    {
        $this->expectException(TransformationFailedException::class);

        $this->productPriceService
            ->method('getPriceById')
            ->with(self::EXAMPLE_PRICE_ID)
            ->willThrowException($exception);

        $this->transformer->reverseTransform(self::EXAMPLE_PRICE_ID);
    }

    /**
     * @return iterable<string, array{\Exception}>
     */
    public function dataProviderForTestReverseTransformHandleProductLoadFailure(): iterable
    {
        yield NotFoundException::class => [
            $this->createMock(NotFoundException::class),
        ];
    }
}
