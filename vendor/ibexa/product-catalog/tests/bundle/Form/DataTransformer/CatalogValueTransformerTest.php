<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Form\DataTransformer;

use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\CatalogValueTransformer;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\ProductCatalog\CatalogServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Throwable;

/**
 * @covers \Ibexa\Bundle\ProductCatalog\Form\DataTransformer\CatalogValueTransformer
 */
final class CatalogValueTransformerTest extends TestCase
{
    private const CATALOG_ID = 123;

    /** @var \Ibexa\Contracts\ProductCatalog\CatalogServiceInterface&\PHPUnit\Framework\MockObject\MockObject */
    private CatalogServiceInterface $catalogService;

    private CatalogValueTransformer $catalogValueTransformer;

    protected function setUp(): void
    {
        $this->catalogService = $this->createMock(CatalogServiceInterface::class);
        $this->catalogValueTransformer = new CatalogValueTransformer($this->catalogService);
    }

    public function testTransform(): void
    {
        $catalog = $this->createMock(CatalogInterface::class);

        $this->mockCatalogServiceGetCatalog(self::CATALOG_ID, $catalog);

        self::assertEquals(
            $catalog,
            $this->catalogValueTransformer->transform(self::CATALOG_ID)
        );
    }

    /**
     * @dataProvider provideDataForTestReverseTransformDataProvider
     */
    public function testReverseTransform(?int $expected, ?CatalogInterface $value): void
    {
        self::assertEquals($expected, $this->catalogValueTransformer->reverseTransform($value));
    }

    public function testTransformThrowTransformationFailedException(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage("Invalid data, expected a integer'ish value");

        /** @phpstan-ignore-next-line */
        $this->catalogValueTransformer->transform([]);
    }

    public function testReverseTransformThrowTransformationFailedException(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected a ' . CatalogInterface::class . ' object.');

        /** @phpstan-ignore-next-line */
        $this->catalogValueTransformer->reverseTransform([]);
    }

    /**
     * @dataProvider provideDataForTestReverseTransformThrowTransformationFailedExceptionForGetCatalog
     */
    public function testReverseTransformThrowTransformationFailedExceptionForGetCatalog(
        Throwable $exception
    ): void {
        $this->expectException(TransformationFailedException::class);

        $this->mockCatalogServiceGetCatalog(self::CATALOG_ID, null, $exception);

        /** @phpstan-ignore-next-line */
        $this->catalogValueTransformer->reverseTransform(self::CATALOG_ID);
    }

    /**
     * @return iterable<array{
     *     ?int,
     *     ?\Ibexa\Contracts\ProductCatalog\Values\CatalogInterface
     * }>
     */
    public function provideDataForTestReverseTransformDataProvider(): iterable
    {
        $catalog = $this->createMock(CatalogInterface::class);
        $catalog->method('getId')->willReturn(self::CATALOG_ID);

        yield 'null' => [null, null];
        yield 'Catalog' => [self::CATALOG_ID, $catalog];
    }

    private function mockCatalogServiceGetCatalog(
        int $catalogId,
        ?CatalogInterface $catalog,
        ?Throwable $exception = null
    ): void {
        $catalogService = $this->catalogService
            ->method('getCatalog')
            ->with($catalogId);

        if (null !== $catalog) {
            $catalogService->willReturn($catalog);
        }

        if (null !== $exception) {
            $catalogService->willThrowException($exception);
        }
    }

    /**
     * @return iterable<array{Throwable}>
     */
    public function provideDataForTestReverseTransformThrowTransformationFailedExceptionForGetCatalog(): iterable
    {
        yield NotFoundException::class => [
            $this->createMock(NotFoundException::class),
        ];

        yield UnauthorizedException::class => [
            $this->createMock(UnauthorizedException::class),
        ];
    }
}
