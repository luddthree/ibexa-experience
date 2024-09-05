<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\View\Matcher\ProductBased;

use Ibexa\Bundle\ProductCatalog\View\Matcher\ProductBased\IsBaseProduct;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Core\MVC\Symfony\Matcher\ContentBased\MatcherInterface;

final class IsBaseProductTest extends AbstractProductMatcherTest
{
    /**
     * @dataProvider dataProviderForTestMatch
     */
    public function testMatchContentInfo(
        ProductInterface $product,
        bool $expectedResult
    ): void {
        $matcher = $this->createMatcher();
        $contentInfo = $this->createContentInfo(true, $product);

        self::assertEquals($expectedResult, $matcher->matchContentInfo($contentInfo));
    }

    /**
     * @dataProvider dataProviderForTestMatch
     */
    public function testMatchLocation(
        ProductInterface $product,
        bool $expectedResult
    ): void {
        $matcher = $this->createMatcher();
        $location = $this->createLocation(true, $product);

        self::assertEquals($expectedResult, $matcher->matchLocation($location));
    }

    /**
     * @dataProvider dataProviderForTestMatch
     */
    public function testMatch(
        ProductInterface $product,
        bool $expectedResult
    ): void {
        $matcher = $this->createMatcher();
        $view = $this->createContentValueView(true, $product);

        self::assertEquals($expectedResult, $matcher->match($view));
    }

    /**
     * @return iterable<string,array{\Ibexa\Contracts\ProductCatalog\Values\ProductInterface,bool}>
     */
    public function dataProviderForTestMatch(): iterable
    {
        yield 'true' => [
            $this->createProduct(true),
            true,
        ];

        yield 'false' => [
            $this->createProduct(false),
            false,
        ];
    }

    protected function createMatcher($matchingConfig = null): MatcherInterface
    {
        $matcher = new IsBaseProduct($this->productService);
        $matcher->setMatchingConfig($matchingConfig);

        return $matcher;
    }

    private function createProduct(bool $isBaseProduct): ProductInterface
    {
        $product = $this->createMock(ProductInterface::class);
        $product->method('isBaseProduct')->willReturn($isBaseProduct);

        return $product;
    }
}
