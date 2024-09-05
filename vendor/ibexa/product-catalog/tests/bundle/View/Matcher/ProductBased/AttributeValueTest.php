<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\View\Matcher\ProductBased;

use Ibexa\Bundle\ProductCatalog\View\Matcher\ProductBased\AttributeValue;
use Ibexa\Contracts\ProductCatalog\Values\AttributeInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Core\MVC\Symfony\Matcher\ContentBased\MatcherInterface;

final class AttributeValueTest extends AbstractProductMatcherTest
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
        $product = $this->createProductWithAttributes([
            'foo' => 'foo',
            'bar' => 'bar',
            'baz' => 'baz',
        ]);

        yield 'match single' => [
            ['foo' => 'foo'],
            $product,
            true,
        ];

        yield 'match multiple' => [
            ['foo' => 'foo', 'bar' => 'bar'],
            $product,
            true,
        ];

        yield 'miss all values' => [
            ['foo' => 'miss', 'bar' => 'miss'],
            $product,
            false,
        ];

        yield 'miss single value' => [
            ['foo' => 'foo', 'bar' => 'miss'],
            $product,
            false,
        ];

        yield 'miss attribute' => [
            ['attribute' => null],
            $product,
            false,
        ];
    }

    protected function createMatcher($matchingConfig = null): MatcherInterface
    {
        $matcher = new AttributeValue($this->productService);
        $matcher->setMatchingConfig($matchingConfig);

        return $matcher;
    }

    /**
     * @param array<string,mixed> $attributes
     */
    private function createProductWithAttributes(array $attributes): ProductInterface
    {
        $collection = [];
        foreach ($attributes as $identifier => $value) {
            $collection[] = $this->createAttribute($identifier, $value);
        }

        $product = $this->createMock(ProductInterface::class);
        $product->method('getAttributes')->willReturn($collection);

        return $product;
    }

    /**
     * @param mixed $value
     */
    private function createAttribute(string $identifier, $value): AttributeInterface
    {
        $attribute = $this->createMock(AttributeInterface::class);
        $attribute->method('getIdentifier')->willReturn($identifier);
        $attribute->method('getValue')->willReturn($value);

        return $attribute;
    }
}
