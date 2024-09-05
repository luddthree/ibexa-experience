<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\View;

use Ibexa\Bundle\ProductCatalog\View\PriceUpdateView;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * @covers \Ibexa\Bundle\ProductCatalog\View\PriceUpdateView
 */
final class PriceUpdateViewTest extends AbstractViewTest
{
    public function provideForParameterTest(): iterable
    {
        $product = $this->createMock(ProductInterface::class);
        $currency = $this->createMock(CurrencyInterface::class);
        $price = $this->createPriceMock($currency, $product);

        $form = $this->createMock(FormInterface::class);
        $formView = $this->createMock(FormView::class);
        $form->expects(self::atLeastOnce())
            ->method('createView')
            ->willReturn($formView);

        $view = new PriceUpdateView('example.html.twig', $price, $form);

        yield [
            $view,
            [
                'price' => $price,
                'form' => $formView,
                'product' => $product,
                'currency' => $currency,
            ],
        ];
    }

    public function provideForOverrideTest(): iterable
    {
        $product = $this->createMock(ProductInterface::class);
        $currency = $this->createMock(CurrencyInterface::class);
        $price = $this->createPriceMock($currency, $product);

        $form = $this->createMock(FormInterface::class);
        $formView = $this->createMock(FormView::class);
        $form->expects(self::atLeastOnce())
            ->method('createView')
            ->willReturn($formView);

        $view = new PriceUpdateView('foo_template', $price, $form);
        $view->setViewType('foo_view_type');
        $view->setParameters(['foo_param' => 'bar']);

        yield [
            $view,
            [
                'price' => $price,
                'form' => $formView,
                'product' => $product,
                'currency' => $currency,
                'foo_param' => 'bar',
            ],
            'foo_template',
            'foo_view_type',
        ];
    }

    private function createPriceMock(CurrencyInterface $currency, ProductInterface $product): PriceInterface
    {
        $price = $this->createMock(PriceInterface::class);
        $price
            ->method('getCurrency')
            ->willReturn($currency);

        $price
            ->method('getProduct')
            ->willReturn($product);

        return $price;
    }
}
