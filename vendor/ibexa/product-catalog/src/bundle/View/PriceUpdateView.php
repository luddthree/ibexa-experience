<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\View;

use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;
use Ibexa\Core\MVC\Symfony\View\BaseView;
use Symfony\Component\Form\FormInterface;

final class PriceUpdateView extends BaseView
{
    private PriceInterface $price;

    private FormInterface $form;

    public function __construct($templateIdentifier, PriceInterface $price, FormInterface $form)
    {
        parent::__construct($templateIdentifier);

        $this->price = $price;
        $this->form = $form;
    }

    public function getPrice(): PriceInterface
    {
        return $this->price;
    }

    public function setPrice(PriceInterface $price): void
    {
        $this->price = $price;
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
            'product' => $this->price->getProduct(),
            'currency' => $this->price->getCurrency(),
            'price' => $this->price,
            'form' => $this->form->createView(),
        ];
    }
}
