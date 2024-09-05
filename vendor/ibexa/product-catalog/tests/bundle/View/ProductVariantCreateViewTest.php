<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\View;

use Ibexa\Bundle\ProductCatalog\View\ProductVariantCreateView;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

final class ProductVariantCreateViewTest extends AbstractViewTest
{
    public function provideForParameterTest(): iterable
    {
        $product = $this->createMock(ProductInterface::class);

        $formView = $this->createMock(FormView::class);

        $form = $this->createMock(FormInterface::class);
        $form->method('createView')->willReturn($formView);

        yield [
            new ProductVariantCreateView('example.html.twig', $product, $form),
            [
                'form' => $formView,
                'product' => $product,
            ],
        ];
    }

    public function provideForOverrideTest(): iterable
    {
        $product = $this->createMock(ProductInterface::class);

        $fromView = $this->createMock(FormView::class);

        $form = $this->createMock(FormInterface::class);
        $form->method('createView')->willReturn($fromView);

        $view = new ProductVariantCreateView('example.html.twig', $product, $form);
        $view->setTemplateIdentifier('override.html.twig');
        $view->setParameters(['custom' => 'value']);
        $view->setViewType('custom_type');

        yield [
            $view,
            [
                'custom' => 'value',
                'form' => $fromView,
                'product' => $product,
            ],
            'override.html.twig',
            'custom_type',
        ];
    }
}
