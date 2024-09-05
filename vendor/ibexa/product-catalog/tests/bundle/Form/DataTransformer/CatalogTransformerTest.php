<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Form\DataTransformer;

use Exception;
use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\CatalogTransformer;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\ProductCatalog\CatalogServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class CatalogTransformerTest extends TestCase
{
    private const EXAMPLE_CATALOG_ID = 14;

    /** @var \Ibexa\Contracts\ProductCatalog\CatalogServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private CatalogServiceInterface $catalogService;

    private CatalogTransformer $transformer;

    protected function setUp(): void
    {
        $this->catalogService = $this->createMock(CatalogServiceInterface::class);
        $this->transformer = new CatalogTransformer($this->catalogService);
    }

    /**
     * @dataProvider dataProviderForTestTransformDataProvider
     */
    public function testTransform(?CatalogInterface $value, ?int $expected): void
    {
        self::assertEquals($expected, $this->transformer->transform($value));
    }

    /**
     * @return iterable<string,mixed>
     */
    public function dataProviderForTestTransformDataProvider(): iterable
    {
        $catalog = $this->createMock(CatalogInterface::class);
        $catalog->method('getId')->willReturn(self::EXAMPLE_CATALOG_ID);

        yield 'null' => [null, null];
        yield 'Catalog' => [$catalog, self::EXAMPLE_CATALOG_ID];
    }

    public function testTransformWithInvalidInput(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected a ' . CatalogInterface::class . ' object.');

        /** @phpstan-ignore-next-line */
        $this->transformer->transform((object)[]);
    }

    public function testReverseTransform(): void
    {
        $catalog = $this->createMock(CatalogInterface::class);

        $this->catalogService
            ->method('getCatalog')
            ->with(self::EXAMPLE_CATALOG_ID)
            ->willReturn($catalog);

        self::assertEquals(
            $catalog,
            $this->transformer->reverseTransform(self::EXAMPLE_CATALOG_ID)
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
        yield 'bool' => [true];
        yield 'non-numeric string' => ['foo'];
        yield 'array' => [['element']];
        yield 'object' => [new stdClass()];
    }

    /**
     * @dataProvider dataProviderForTestReverseTransformHandleProductLoadFailure
     */
    public function testReverseTransformHandleProductLoadFailure(Exception $exception): void
    {
        $this->expectException(TransformationFailedException::class);

        $this->catalogService
            ->method('getCatalog')
            ->with(self::EXAMPLE_CATALOG_ID)
            ->willThrowException($exception);

        $this->transformer->reverseTransform(self::EXAMPLE_CATALOG_ID);
    }

    /**
     * @return iterable<string,mixed>
     */
    public function dataProviderForTestReverseTransformHandleProductLoadFailure(): iterable
    {
        yield NotFoundException::class => [
            $this->createMock(NotFoundException::class),
        ];

        yield UnauthorizedException::class => [
            $this->createMock(UnauthorizedException::class),
        ];
    }
}
