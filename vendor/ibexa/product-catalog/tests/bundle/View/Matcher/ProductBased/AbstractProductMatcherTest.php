<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\View\Matcher\ProductBased;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\ProductCatalog\Local\LocalProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Core\MVC\Symfony\Matcher\ContentBased\MatcherInterface;
use Ibexa\Core\MVC\Symfony\View\BaseView;
use Ibexa\Core\MVC\Symfony\View\ContentValueView;
use Ibexa\Core\MVC\Symfony\View\View;
use PHPUnit\Framework\TestCase;

abstract class AbstractProductMatcherTest extends TestCase
{
    /** @var \Ibexa\Contracts\ProductCatalog\Local\LocalProductServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    protected LocalProductServiceInterface $productService;

    protected function setUp(): void
    {
        $this->productService = $this->createMock(LocalProductServiceInterface::class);
    }

    public function testMatchContentInfoWithNonProduct(): void
    {
        self::assertFalse($this->createMatcher()->matchContentInfo($this->createContentInfo(false)));
    }

    public function testMatchLocationWithNonProduct(): void
    {
        self::assertFalse($this->createMatcher()->matchLocation($this->createLocation(false)));
    }

    public function testMatchNonProduct(): void
    {
        self::assertFalse($this->createMatcher()->match($this->createContentValueView(false)));
    }

    public function testMatchNonContentValueView(): void
    {
        self::assertFalse($this->createMatcher()->match($this->createMock(View::class)));
    }

    /**
     * @param mixed $matchingConfig
     */
    abstract protected function createMatcher($matchingConfig = null): MatcherInterface;

    /**
     * @return \Ibexa\Core\MVC\Symfony\View\View&\Ibexa\Core\MVC\Symfony\View\ContentValueView
     */
    protected function createContentValueView(
        bool $withProductSpecification,
        ?ProductInterface $product = null
    ) {
        $content = $this->createContent($withProductSpecification, $product);

        return new class($content) extends BaseView implements ContentValueView {
            private Content $content;

            public function __construct(Content $content)
            {
                parent::__construct();

                $this->content = $content;
            }

            public function getContent(): Content
            {
                return $this->content;
            }
        };
    }

    protected function createLocation(
        bool $withProductSpecification,
        ?ProductInterface $product = null
    ): Location {
        $location = $this->createMock(Location::class);
        $location->method('getContent')->willReturn($this->createContent($withProductSpecification, $product));

        return $location;
    }

    protected function createContent(
        bool $withProductSpecification,
        ?ProductInterface $product = null
    ): Content {
        $content = $this->createMock(Content::class);

        $this->productService->method('isProduct')->with($content)->willReturn($withProductSpecification);
        if ($withProductSpecification) {
            $product ??= $this->createMock(ProductInterface::class);

            $this->productService->method('getProductFromContent')->with($content)->willReturn($product);
        }

        return $content;
    }

    protected function createContentInfo(
        bool $withProductSpecification,
        ?ProductInterface $product = null
    ): ContentInfo {
        $contentInfo = $this->createMock(ContentInfo::class);
        $contentInfo->method('getMainLocation')->willReturn($this->createLocation($withProductSpecification, $product));

        return $contentInfo;
    }
}
