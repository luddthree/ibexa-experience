<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\View;

use Ibexa\Bundle\ProductCatalog\View\ProductListView;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * @covers \Ibexa\Bundle\ProductCatalog\View\ProductListView
 */
final class ProductListViewTest extends AbstractViewTest
{
    public function provideForParameterTest(): iterable
    {
        $products = [
            $this->createMock(ProductInterface::class),
            $this->createMock(ProductInterface::class),
            $this->createMock(ProductInterface::class),
        ];

        $searchFormView = $this->createMock(FormView::class);

        $searchForm = $this->createMock(FormInterface::class);
        $searchForm->method('createView')->willReturn($searchFormView);

        $category = $this->createMock(TaxonomyEntry::class);

        $categoryWithFormDataUrlTemplate = 'sample_url';

        $view = new ProductListView(
            'example.html.twig',
            $products,
            $searchForm,
            $category,
            $categoryWithFormDataUrlTemplate
        );
        $view->setEditable(true);

        yield [
            $view,
            [
                'is_editable' => true,
                'products' => $products,
                'search_form' => $searchFormView,
                'category_entry' => $category,
                'category_with_form_data_url_template' => 'sample_url',
            ],
        ];
    }

    public function provideForOverrideTest(): iterable
    {
        $products = [
            $this->createMock(ProductInterface::class),
            $this->createMock(ProductInterface::class),
            $this->createMock(ProductInterface::class),
        ];

        $searchFormView = $this->createMock(FormView::class);

        $searchForm = $this->createMock(FormInterface::class);
        $searchForm->method('createView')->willReturn($searchFormView);

        $category = $this->createMock(TaxonomyEntry::class);

        $categoryWithFormDataUrlTemplate = 'sample_url';

        $view = new ProductListView(
            'foo_template',
            $products,
            $searchForm,
            $category,
            $categoryWithFormDataUrlTemplate
        );
        $view->setParameters(['foo_param' => 'bar']);
        $view->setViewType('foo_view_type');
        $view->setEditable(true);

        yield [
            $view,
            [
                'is_editable' => true,
                'products' => $products,
                'search_form' => $searchFormView,
                'foo_param' => 'bar',
                'category_entry' => $category,
                'category_with_form_data_url_template' => $categoryWithFormDataUrlTemplate,
            ],
            'foo_template',
            'foo_view_type',
        ];
    }
}
