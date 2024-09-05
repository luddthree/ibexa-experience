<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\View;

use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\Values\AttributeTypeInterface;
use Ibexa\Core\MVC\Symfony\View\BaseView;
use Symfony\Component\Form\FormInterface;

final class AttributeDefinitionCreateView extends BaseView
{
    private FormInterface $form;

    private AttributeTypeInterface $attributeType;

    private Language $language;

    public function __construct(
        $templateIdentifier,
        FormInterface $form,
        AttributeTypeInterface $attributeType,
        Language $language
    ) {
        parent::__construct($templateIdentifier);

        $this->form = $form;
        $this->attributeType = $attributeType;
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

    public function getAttributeType(): AttributeTypeInterface
    {
        return $this->attributeType;
    }

    public function setAttributeType(AttributeTypeInterface $attributeType): void
    {
        $this->attributeType = $attributeType;
    }

    public function getLanguage(): Language
    {
        return $this->language;
    }

    public function setLanguage(Language $language): void
    {
        $this->language = $language;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getInternalParameters(): array
    {
        return [
            'attribute_type' => $this->attributeType,
            'form' => $this->form->createView(),
            'language' => $this->language,
        ];
    }
}
