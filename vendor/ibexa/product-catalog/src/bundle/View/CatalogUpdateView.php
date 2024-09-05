<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\View;

use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;
use Ibexa\Core\MVC\Symfony\View\BaseView;
use Symfony\Component\Form\FormInterface;

final class CatalogUpdateView extends BaseView
{
    private CatalogInterface $catalog;

    private FormInterface $form;

    /** @var \Ibexa\Contracts\ProductCatalog\Values\ProductInterface[] */
    private iterable $products;

    /**
     * @param iterable<\Ibexa\Contracts\ProductCatalog\Values\ProductInterface> $products
     */
    public function __construct(
        string $templateIdentifier,
        CatalogInterface $catalog,
        FormInterface $form,
        iterable $products
    ) {
        parent::__construct($templateIdentifier);

        $this->catalog = $catalog;
        $this->form = $form;
        $this->products = $products;
    }

    public function getCatalog(): CatalogInterface
    {
        return $this->catalog;
    }

    public function setCatalog(CatalogInterface $catalog): void
    {
        $this->catalog = $catalog;
    }

    public function getForm(): FormInterface
    {
        return $this->form;
    }

    public function setForm(FormInterface $form): void
    {
        $this->form = $form;
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

    /**
     * @return array<string,mixed>
     */
    protected function getInternalParameters(): array
    {
        return [
            'catalog' => $this->catalog,
            'form' => $this->form->createView(),
            'products' => $this->products,
        ];
    }
}
