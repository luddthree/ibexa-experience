<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\View;

use Ibexa\Core\MVC\Symfony\View\BaseView;
use Symfony\Component\Form\FormInterface;

final class ProductTypeListView extends BaseView
{
    /** @var iterable<\Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface> */
    private iterable $productTypes;

    private FormInterface $searchForm;

    private bool $editable = false;

    /**
     * @param iterable<\Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface> $productTypes
     */
    public function __construct($templateIdentifier, iterable $productTypes, FormInterface $searchForm)
    {
        parent::__construct($templateIdentifier);

        $this->productTypes = $productTypes;
        $this->searchForm = $searchForm;
    }

    /**
     * @return iterable<\Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface>
     */
    public function getProductTypes(): iterable
    {
        return $this->productTypes;
    }

    /**
     * @param iterable<\Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface> $productTypes
     */
    public function setProductTypes(iterable $productTypes): void
    {
        $this->productTypes = $productTypes;
    }

    public function getSearchForm(): ?FormInterface
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

    /**
     * @return array<string,mixed>
     */
    protected function getInternalParameters(): array
    {
        return [
            'is_editable' => $this->editable,
            'product_types' => $this->productTypes,
            'search_form' => $this->searchForm->createView(),
        ];
    }
}
