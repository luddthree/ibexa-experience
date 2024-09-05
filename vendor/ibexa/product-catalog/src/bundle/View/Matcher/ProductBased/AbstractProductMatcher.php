<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\View\Matcher\ProductBased;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\ProductCatalog\Local\LocalProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Core\MVC\Symfony\Matcher\ContentBased\MatcherInterface;
use Ibexa\Core\MVC\Symfony\View\ContentValueView;
use Ibexa\Core\MVC\Symfony\View\View;

abstract class AbstractProductMatcher implements MatcherInterface
{
    protected LocalProductServiceInterface $productService;

    public function __construct(LocalProductServiceInterface $productService)
    {
        $this->productService = $productService;
    }

    public function matchLocation(Location $location): bool
    {
        return $this->matchContent($location->getContent());
    }

    public function matchContentInfo(ContentInfo $contentInfo): bool
    {
        $location = $contentInfo->getMainLocation();
        if ($location !== null) {
            return $this->matchContent($location->getContent());
        }

        return false;
    }

    public function match(View $view): bool
    {
        if (!$view instanceof ContentValueView) {
            return false;
        }

        return $this->matchContent($view->getContent());
    }

    private function matchContent(Content $content): bool
    {
        if ($this->productService->isProduct($content)) {
            return $this->matchProduct($this->productService->getProductFromContent($content));
        }

        return false;
    }

    abstract protected function matchProduct(ProductInterface $product): bool;
}
