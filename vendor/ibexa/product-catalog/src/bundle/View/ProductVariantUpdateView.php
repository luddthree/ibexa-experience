<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\View;

use Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface;
use Ibexa\Core\MVC\Symfony\View\BaseView;
use Symfony\Component\Form\FormInterface;

final class ProductVariantUpdateView extends BaseView
{
    private ProductVariantInterface $variant;

    private FormInterface $form;

    public function __construct($templateIdentifier, ProductVariantInterface $variant, FormInterface $form)
    {
        parent::__construct($templateIdentifier);

        $this->variant = $variant;
        $this->form = $form;
    }

    public function getVariant(): ProductVariantInterface
    {
        return $this->variant;
    }

    public function setVariant(ProductVariantInterface $variant): void
    {
        $this->variant = $variant;
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
     * @return array<string,mixed>
     */
    protected function getInternalParameters(): array
    {
        return [
            'form' => $this->form->createView(),
            'variant' => $this->variant,
        ];
    }
}
