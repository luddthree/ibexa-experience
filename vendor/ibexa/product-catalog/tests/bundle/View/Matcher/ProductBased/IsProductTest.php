<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\View\Matcher\ProductBased;

use Ibexa\Bundle\ProductCatalog\View\Matcher\ProductBased\IsProduct;
use Ibexa\Core\MVC\Symfony\Matcher\ContentBased\MatcherInterface;

final class IsProductTest extends AbstractProductMatcherTest
{
    public function testMatchContentInfo(): void
    {
        self::assertTrue($this->createMatcher()->matchContentInfo($this->createContentInfo(true)));
    }

    public function testMatchLocation(): void
    {
        self::assertTrue($this->createMatcher()->matchLocation($this->createLocation(true)));
    }

    public function testMatch(): void
    {
        self::assertTrue($this->createMatcher()->match($this->createContentValueView(true)));
    }

    protected function createMatcher($matchingConfig = null): MatcherInterface
    {
        $matcher = new IsProduct($this->productService);
        $matcher->setMatchingConfig($matchingConfig);

        return $matcher;
    }
}
