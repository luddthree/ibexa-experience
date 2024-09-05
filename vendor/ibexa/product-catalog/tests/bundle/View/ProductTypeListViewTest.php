<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\View;

use Ibexa\Bundle\ProductCatalog\View\ProductTypeListView;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * @covers \Ibexa\Bundle\ProductCatalog\View\ProductTypeListView
 */
final class ProductTypeListViewTest extends AbstractViewTest
{
    public function provideForParameterTest(): iterable
    {
        $productTypes = $this->createProductTypesMock();
        [$searchForm, $searchFormView] = $this->createSearchFormMock();

        $view = new ProductTypeListView('list.html.twig', $productTypes, $searchForm);
        $view->setEditable(true);

        yield [
            $view,
            [
                'is_editable' => true,
                'product_types' => $productTypes,
                'search_form' => $searchFormView,
            ],
        ];
    }

    public function provideForOverrideTest(): iterable
    {
        $productTypes = $this->createProductTypesMock();
        [$searchForm, $searchFormView] = $this->createSearchFormMock();

        $view = new ProductTypeListView('list.html.twig', $productTypes, $searchForm);
        $view->setEditable(true);
        $view->setTemplateIdentifier('custom.html.twig');
        $view->setViewType('embed');
        $view->addParameters([
            'foo' => 'foo',
            'bar' => 'bar',
        ]);

        yield [
            $view,
            [
                'is_editable' => true,
                'product_types' => $productTypes,
                'search_form' => $searchFormView,
                'foo' => 'foo',
                'bar' => 'bar',
            ],
            'custom.html.twig',
            'embed',
        ];
    }

    /**
     * @return \Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface[]
     */
    private function createProductTypesMock(): array
    {
        return [
            $this->createMock(ProductTypeInterface::class),
            $this->createMock(ProductTypeInterface::class),
            $this->createMock(ProductTypeInterface::class),
        ];
    }

    /**
     * @return array{\Symfony\Component\Form\FormInterface,\Symfony\Component\Form\FormView}
     */
    private function createSearchFormMock(): array
    {
        $searchFormView = $this->createMock(FormView::class);

        $searchForm = $this->createMock(FormInterface::class);
        $searchForm->method('createView')->willReturn($searchFormView);

        return [$searchForm, $searchFormView];
    }
}
