<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\View;

use Ibexa\Bundle\ProductCatalog\View\ProductVariantUpdateView;
use Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

final class ProductVariantUpdateViewTest extends AbstractViewTest
{
    public function provideForParameterTest(): iterable
    {
        $variant = $this->createMock(ProductVariantInterface::class);

        $formView = $this->createMock(FormView::class);

        $form = $this->createMock(FormInterface::class);
        $form->method('createView')->willReturn($formView);

        yield [
            new ProductVariantUpdateView('example.html.twig', $variant, $form),
            [
                'form' => $formView,
                'variant' => $variant,
            ],
        ];
    }

    public function provideForOverrideTest(): iterable
    {
        $variant = $this->createMock(ProductVariantInterface::class);

        $formView = $this->createMock(FormView::class);

        $form = $this->createMock(FormInterface::class);
        $form->method('createView')->willReturn($formView);

        $view = new ProductVariantUpdateView('example.html.twig', $variant, $form);
        $view->setTemplateIdentifier('override.html.twig');
        $view->setParameters(['custom' => 'value']);
        $view->setViewType('custom_type');

        yield [
            $view,
            [
                'custom' => 'value',
                'form' => $formView,
                'variant' => $variant,
            ],
            'override.html.twig',
            'custom_type',
        ];
    }
}
