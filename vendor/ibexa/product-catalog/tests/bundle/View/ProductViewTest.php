<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\View;

use Ibexa\Bundle\ProductCatalog\View\ProductView;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;

/**
 * @covers \Ibexa\Bundle\ProductCatalog\View\ProductView
 */
final class ProductViewTest extends AbstractViewTest
{
    /**
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentType
     */
    public function provideForParameterTest(): iterable
    {
        $product = $this->createMock(ProductInterface::class);
        $view = new ProductView('example.html.twig', $product);

        yield [
            $view,
            [
                'product' => $product,
                'is_editable' => false,
            ],
        ];
    }

    /**
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentType
     */
    public function provideForOverrideTest(): iterable
    {
        $product = $this->createMock(ProductInterface::class);
        $view = new ProductView('foo_template', $product);
        $view->setParameters(['param_foo' => 'bar']);
        $view->setViewType('foo_view_type');

        yield [
            $view,
            [
                'product' => $product,
                'param_foo' => 'bar',
                'is_editable' => false,
            ],
            'foo_template',
            'foo_view_type',
        ];
    }
}
