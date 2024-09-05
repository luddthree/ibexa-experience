<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\View;

use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Core\MVC\Symfony\View\BaseView;
use Symfony\Component\Form\FormInterface;

abstract class AbstractAvailabilityFormView extends BaseView
{
    private ProductInterface $product;

    private FormInterface $form;

    public function __construct($templateIdentifier, ProductInterface $product, FormInterface $form)
    {
        parent::__construct($templateIdentifier);

        $this->product = $product;
        $this->form = $form;
    }

    public function getProduct(): ProductInterface
    {
        return $this->product;
    }

    public function setProduct(ProductInterface $product): void
    {
        $this->product = $product;
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
     * @return array<string, mixed>
     */
    protected function getInternalParameters(): array
    {
        return [
            'product' => $this->product,
            'form' => $this->form->createView(),
        ];
    }
}
