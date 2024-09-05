<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\View;

use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\Core\MVC\Symfony\View\BaseView;
use Symfony\Component\Form\FormInterface;

final class ProductCreateView extends BaseView
{
    private ProductTypeInterface $productType;

    private Language $language;

    private FormInterface $form;

    public function __construct(
        $templateIdentifier,
        ProductTypeInterface $productType,
        Language $language,
        FormInterface $form
    ) {
        parent::__construct($templateIdentifier);

        $this->productType = $productType;
        $this->language = $language;
        $this->form = $form;
    }

    public function getProductType(): ProductTypeInterface
    {
        return $this->productType;
    }

    public function setProductType(ProductTypeInterface $productType): void
    {
        $this->productType = $productType;
    }

    public function getLanguage(): Language
    {
        return $this->language;
    }

    public function setLanguage(Language $language): void
    {
        $this->language = $language;
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
            'language' => $this->language,
            'product_type' => $this->productType,
        ];
    }
}
