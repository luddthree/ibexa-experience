<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\View;

use Ibexa\Bundle\ProductCatalog\View\ProductTypeView;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Bundle\ProductCatalog\View\ProductTypeView
 */
final class ProductTypeViewTest extends TestCase
{
    public function testGetParameters(): void
    {
        $productType = $this->createMock(ProductTypeInterface::class);

        $view = new ProductTypeView('view.html.twig', $productType);

        self::assertEquals([
            'is_editable' => false,
            'product_type' => $productType,
        ], $view->getParameters());
    }
}
