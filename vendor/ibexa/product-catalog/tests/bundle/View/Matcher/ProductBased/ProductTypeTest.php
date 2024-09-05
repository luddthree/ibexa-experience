<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\View\Matcher\ProductBased;

use Ibexa\Bundle\ProductCatalog\View\Matcher\ProductBased\ProductType;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\Core\MVC\Symfony\Matcher\ContentBased\MatcherInterface;

final class ProductTypeTest extends AbstractProductMatcherTest
{
    /**
     * @param mixed $matchingConfig
     *
     * @dataProvider dataProviderForTestMatch
     */
    public function testMatchContentInfo(
        $matchingConfig,
        ProductInterface $product,
        bool $expectedResult
    ): void {
        $matcher = $this->createMatcher($matchingConfig);
        $contentInfo = $this->createContentInfo(true, $product);

        self::assertEquals($expectedResult, $matcher->matchContentInfo($contentInfo));
    }

    /**
     * @param mixed $matchingConfig
     *
     * @dataProvider dataProviderForTestMatch
     */
    public function testMatchLocation(
        $matchingConfig,
        ProductInterface $product,
        bool $expectedResult
    ): void {
        $matcher = $this->createMatcher($matchingConfig);
        $location = $this->createLocation(true, $product);

        self::assertEquals($expectedResult, $matcher->matchLocation($location));
    }

    /**
     * @param mixed $matchingConfig
     *
     * @dataProvider dataProviderForTestMatch
     */
    public function testMatch(
        $matchingConfig,
        ProductInterface $product,
        bool $expectedResult
    ): void {
        $matcher = $this->createMatcher($matchingConfig);
        $view = $this->createContentValueView(true, $product);

        self::assertEquals($expectedResult, $matcher->match($view));
    }

    /**
     * @return iterable<string,array{mixed,ProductInterface,bool}>
     */
    public function dataProviderForTestMatch(): iterable
    {
        yield 'match' => [
            ['foo', 'bar', 'baz'],
            $this->createProductWithType('foo'),
            true,
        ];

        yield 'miss' => [
            ['foo', 'bar', 'baz'],
            $this->createProductWithType('foobar'),
            false,
        ];

        yield 'empty' => [
            null,
            $this->createProductWithType('foobar'),
            false,
        ];
    }

    protected function createMatcher($matchingConfig = null): MatcherInterface
    {
        $matcher = new ProductType($this->productService);
        $matcher->setMatchingConfig($matchingConfig);

        return $matcher;
    }

    private function createProductWithType(string $identifier): ProductInterface
    {
        $productType = $this->createMock(ProductTypeInterface::class);
        $productType->method('getIdentifier')->willReturn($identifier);

        $product = $this->createMock(ProductInterface::class);
        $product->method('getProductType')->willReturn($productType);

        return $product;
    }
}
