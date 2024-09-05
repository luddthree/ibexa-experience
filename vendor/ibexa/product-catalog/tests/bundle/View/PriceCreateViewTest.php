<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\View;

use Ibexa\Bundle\ProductCatalog\View\PriceCreateView;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * @covers \Ibexa\Bundle\ProductCatalog\View\PriceCreateView
 */
final class PriceCreateViewTest extends AbstractViewTest
{
    public function provideForParameterTest(): iterable
    {
        $product = $this->createMock(ProductInterface::class);
        $currency = $this->createMock(CurrencyInterface::class);

        $form = $this->createMock(FormInterface::class);
        $formView = $this->createMock(FormView::class);
        $form->expects(self::atLeastOnce())
            ->method('createView')
            ->willReturn($formView);

        $view = new PriceCreateView('example.html.twig', $product, $currency, $form);

        yield [
            $view,
            [
                'product' => $product,
                'currency' => $currency,
                'form' => $formView,
            ],
        ];
    }

    public function provideForOverrideTest(): iterable
    {
        $product = $this->createMock(ProductInterface::class);
        $currency = $this->createMock(CurrencyInterface::class);

        $form = $this->createMock(FormInterface::class);
        $formView = $this->createMock(FormView::class);
        $form->expects(self::atLeastOnce())
            ->method('createView')
            ->willReturn($formView);

        $view = new PriceCreateView('foo_template', $product, $currency, $form);
        $view->setViewType('foo_view_type');
        $view->setParameters(['foo_param' => 'bar']);

        yield [
            $view,
            [
                'product' => $product,
                'currency' => $currency,
                'form' => $formView,
                'foo_param' => 'bar',
            ],
            'foo_template',
            'foo_view_type',
        ];
    }
}
