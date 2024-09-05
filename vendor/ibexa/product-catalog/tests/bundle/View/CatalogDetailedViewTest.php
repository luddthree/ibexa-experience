<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\View;

use Ibexa\Bundle\ProductCatalog\View\CatalogDetailedView;
use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Bundle\ProductCatalog\View\CustomerGroupDetailedView
 */
final class CatalogDetailedViewTest extends TestCase
{
    public function testGetParameters(): void
    {
        $expectedCatalog = $this->createMock(CatalogInterface::class);

        $view = new CatalogDetailedView(
            'example.html.twig',
            $expectedCatalog
        );

        self::assertEquals([
            'catalog' => $expectedCatalog,
            'is_editable' => true,
        ], $view->getParameters());
    }
}
