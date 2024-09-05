<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Twig;

use Ibexa\Bundle\ProductCatalog\Twig\RenderProductExtension;
use Ibexa\Bundle\ProductCatalog\Twig\RenderProductRuntime;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\ProductCatalog\Local\LocalProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\ProductCatalog\Local\Repository\Attribute\ValueFormatterDispatcherInterface;
use Twig\Extension\RuntimeExtensionInterface;
use Twig\RuntimeLoader\RuntimeLoaderInterface;
use Twig\Test\IntegrationTestCase;

final class RenderProductExtensionTest extends IntegrationTestCase
{
    /** @var \Ibexa\Contracts\ProductCatalog\Local\LocalProductServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private LocalProductServiceInterface $productService;

    /** @var \Ibexa\ProductCatalog\Local\Repository\Attribute\ValueFormatterDispatcherInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ValueFormatterDispatcherInterface $valueFormatterDispatcher;

    /**
     * @return \Twig\RuntimeLoader\RuntimeLoaderInterface[]
     */
    public function getRuntimeLoaders(): array
    {
        $this->productService = $this->createProductService();
        $this->valueFormatterDispatcher = $this->createValueFormatterDispatcher();

        return [
            new class($this->productService, $this->valueFormatterDispatcher) implements RuntimeLoaderInterface {
                private LocalProductServiceInterface $productService;

                private ValueFormatterDispatcherInterface $valueFormatterDispatcher;

                public function __construct(
                    LocalProductServiceInterface $productService,
                    ValueFormatterDispatcherInterface $valueFormatterDispatcher
                ) {
                    $this->productService = $productService;
                    $this->valueFormatterDispatcher = $valueFormatterDispatcher;
                }

                public function load(string $class): ?RuntimeExtensionInterface
                {
                    if ($class === RenderProductRuntime::class) {
                        return new RenderProductRuntime(
                            $this->productService,
                            $this->valueFormatterDispatcher
                        );
                    }

                    return null;
                }
            },
        ];
    }

    public function getFixturesDir(): string
    {
        return __DIR__ . '/Fixtures/RenderProductExtension/';
    }

    /**
     * @return \Twig\Extension\ExtensionInterface[]
     */
    protected function getExtensions(): array
    {
        return [
            new RenderProductExtension(),
        ];
    }

    public function getAttribute(string $identifier): AttributeInterface
    {
        $attribute = $this->createMock(AttributeInterface::class);

        $this->valueFormatterDispatcher
            ->method('formatValue')
            ->with($attribute)
            ->willReturn(strtoupper($identifier));

        return $attribute;
    }

    public function getContentWithoutProduct(): Content
    {
        $content = $this->createMock(Content::class);

        $this->productService->method('isProduct')->with($content)->willReturn(false);

        return $content;
    }

    public function getContentWithProduct(ProductInterface $product): Content
    {
        $content = $this->createMock(Content::class);
        $this->productService->method('isProduct')->with($content)->willReturn(true);
        $this->productService->method('getProductFromContent')->with($content)->willReturn($product);

        return $content;
    }

    public function getProduct(string $code): ProductInterface
    {
        $product = $this->createMock(ProductInterface::class);
        $product->method('getCode')->willReturn($code);

        return $product;
    }

    /**
     * @return \Ibexa\Contracts\ProductCatalog\Local\LocalProductServiceInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function createProductService(): LocalProductServiceInterface
    {
        return $this->createMock(LocalProductServiceInterface::class);
    }

    /**
     * @return \Ibexa\ProductCatalog\Local\Repository\Attribute\ValueFormatterDispatcherInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function createValueFormatterDispatcher(): ValueFormatterDispatcherInterface
    {
        return $this->createMock(ValueFormatterDispatcherInterface::class);
    }
}
