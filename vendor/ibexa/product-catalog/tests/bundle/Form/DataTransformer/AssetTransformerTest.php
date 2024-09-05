<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Form\DataTransformer;

use Exception;
use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\AssetTransformer;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\ProductCatalog\AssetServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Asset\AssetInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class AssetTransformerTest extends TestCase
{
    private const EXAMPLE_IDENTIFIER = '1';

    /** @var \Ibexa\Contracts\ProductCatalog\AssetServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private AssetServiceInterface $assetService;

    /** @var \Ibexa\Contracts\ProductCatalog\Values\ProductInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ProductInterface $product;

    private AssetTransformer $transformer;

    protected function setUp(): void
    {
        $this->assetService = $this->createMock(AssetServiceInterface::class);
        $this->product = $this->createMock(ProductInterface::class);
        $this->transformer = new AssetTransformer($this->assetService, $this->product);
    }

    /**
     * @dataProvider dataProviderForTestTransformDataProvider
     */
    public function testTransform(?AssetInterface $value, ?string $expected): void
    {
        self::assertEquals($expected, $this->transformer->transform($value));
    }

    /**
     * @return iterable<string, array<mixed>>
     */
    public function dataProviderForTestTransformDataProvider(): iterable
    {
        $asset = $this->createMock(AssetInterface::class);
        $asset->method('getIdentifier')->willReturn(self::EXAMPLE_IDENTIFIER);

        yield 'null' => [null, null];
        yield 'Asset' => [$asset, self::EXAMPLE_IDENTIFIER];
    }

    public function testTransformWithInvalidInput(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected a Ibexa\Contracts\ProductCatalog\Values\Asset\AssetInterface object, received stdClass.');

        $this->transformer->transform(new stdClass());
    }

    public function testReverseTransform(): void
    {
        $asset = $this->createMock(AssetInterface::class);

        $this->assetService
            ->method('getAsset')
            ->with($this->product, self::EXAMPLE_IDENTIFIER)
            ->willReturn($asset);

        self::assertEquals(
            $asset,
            $this->transformer->reverseTransform(self::EXAMPLE_IDENTIFIER)
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
        $this->expectExceptionMessage('Invalid data, expected a string value');

        $this->transformer->reverseTransform($value);
    }

    /**
     * @return iterable<string,array{mixed}>
     */
    public function dataProviderForTestReverseTransformWithInvalidInputDataProvider(): iterable
    {
        yield 'int' => [2];
        yield 'bool' => [true];
        yield 'float' => [12.34];
        yield 'array' => [['element']];
        yield 'object' => [new stdClass()];
    }

    /**
     * @dataProvider dataProviderForTestReverseTransformHandleProductLoadFailure
     */
    public function testReverseTransformHandleAssetLoadFailure(Exception $exception): void
    {
        $this->expectException(TransformationFailedException::class);

        $this->assetService
            ->method('getAsset')
            ->with($this->product, self::EXAMPLE_IDENTIFIER)
            ->willThrowException($exception);

        $this->transformer->reverseTransform(self::EXAMPLE_IDENTIFIER);
    }

    /**
     * @return iterable<string,mixed>
     */
    public function dataProviderForTestReverseTransformHandleProductLoadFailure(): iterable
    {
        yield NotFoundException::class => [
            $this->createMock(NotFoundException::class),
        ];
    }
}
