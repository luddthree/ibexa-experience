<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Twig;

use Ibexa\Bundle\ProductCatalog\Twig\ProductTypeRuntime;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @covers \Ibexa\Bundle\ProductCatalog\Twig\ProductTypeRuntime
 */
final class ProductTypeRuntimeTest extends TestCase
{
    private ProductTypeRuntime $productTypeRuntime;

    /** @var \Symfony\Contracts\Translation\TranslatorInterface&\PHPUnit\Framework\MockObject\MockObject */
    private TranslatorInterface $translator;

    protected function setUp(): void
    {
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->productTypeRuntime = new ProductTypeRuntime($this->translator);
    }

    /**
     * @dataProvider provideDataForTestGetType
     */
    public function testGetType(
        string $expected,
        string $translationKey,
        ProductTypeInterface $productType
    ): void {
        $this->mockTranslatorTranslate($translationKey, $expected);

        self::assertSame(
            $expected,
            $this->productTypeRuntime->getType($productType)
        );
    }

    /**
     * @return iterable<array{
     *     string,
     *     string,
     *     \Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface
     * }>
     */
    public function provideDataForTestGetType(): iterable
    {
        yield 'Product type virtual' => [
            'Virtual',
            'product_type.virtual',
            $this->createProductType(true),
        ];

        yield 'Product type physical' => [
            'Physical',
            'product_type.physical',
            $this->createProductType(false),
        ];
    }

    private function createProductType(bool $isVirtual): ProductTypeInterface
    {
        $productType = $this->createMock(ProductTypeInterface::class);

        if ($isVirtual) {
            $productType
                ->expects(self::once())
                ->method('isVirtual')
                ->willReturn(true);
        }

        return $productType;
    }

    private function mockTranslatorTranslate(string $key, string $desc): void
    {
        $this->translator
            ->expects(self::once())
            ->method('trans')
            ->with($key, [], 'ibexa_product_catalog')
            ->willReturn($desc);
    }
}
