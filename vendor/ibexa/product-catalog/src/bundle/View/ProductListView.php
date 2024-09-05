<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\View;

use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Core\MVC\Symfony\View\BaseView;
use Symfony\Component\Form\FormInterface;

final class ProductListView extends BaseView
{
    /** @var \Ibexa\Contracts\ProductCatalog\Values\ProductInterface[] */
    private iterable $products;

    private FormInterface $searchForm;

    private bool $editable = false;

    private ?TaxonomyEntry $category;

    private string $categoryWithFormDataUrlTemplate;

    /**
     * @param iterable<\Ibexa\Contracts\ProductCatalog\Values\ProductInterface> $products
     */
    public function __construct(
        $templateIdentifier,
        iterable $products,
        FormInterface $searchForm,
        ?TaxonomyEntry $category,
        string $categoryWithFormDataUrlTemplate
    ) {
        parent::__construct($templateIdentifier);

        $this->products = $products;
        $this->searchForm = $searchForm;
        $this->category = $category;
        $this->categoryWithFormDataUrlTemplate = $categoryWithFormDataUrlTemplate;
    }

    /**
     * @return iterable<\Ibexa\Contracts\ProductCatalog\Values\ProductInterface>
     */
    public function getProducts(): iterable
    {
        return $this->products;
    }

    /**
     * @param iterable<\Ibexa\Contracts\ProductCatalog\Values\ProductInterface> $products
     */
    public function setProducts(iterable $products): void
    {
        $this->products = $products;
    }

    public function getSearchForm(): FormInterface
    {
        return $this->searchForm;
    }

    public function setSearchForm(FormInterface $searchForm): void
    {
        $this->searchForm = $searchForm;
    }

    public function isEditable(): bool
    {
        return $this->editable;
    }

    public function setEditable(bool $editable): void
    {
        $this->editable = $editable;
    }

    public function getCategory(): ?TaxonomyEntry
    {
        return $this->category;
    }

    public function setCategory(?TaxonomyEntry $category): void
    {
        $this->category = $category;
    }

    public function getCategoryWithFormDataUrlTemplate(): string
    {
        return $this->categoryWithFormDataUrlTemplate;
    }

    public function setCategoryWithFormDataUrlTemplate(string $categoryWithFormDataUrlTemplate): void
    {
        $this->categoryWithFormDataUrlTemplate = $categoryWithFormDataUrlTemplate;
    }

    /**
     * @return array<string,mixed>
     */
    protected function getInternalParameters(): array
    {
        return [
            'is_editable' => $this->editable,
            'products' => $this->products,
            'search_form' => $this->searchForm->createView(),
            'category_entry' => $this->category,
            'category_with_form_data_url_template' => $this->categoryWithFormDataUrlTemplate,
        ];
    }
}
