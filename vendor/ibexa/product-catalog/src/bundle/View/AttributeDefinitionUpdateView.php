<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\View;

use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Ibexa\Core\MVC\Symfony\View\BaseView;
use Symfony\Component\Form\FormInterface;

final class AttributeDefinitionUpdateView extends BaseView
{
    private AttributeDefinitionInterface $attributeDefinition;

    private Language $language;

    private FormInterface $form;

    public function __construct(
        $templateIdentifier,
        AttributeDefinitionInterface $attributeDefinition,
        FormInterface $form,
        Language $language
    ) {
        parent::__construct($templateIdentifier);

        $this->attributeDefinition = $attributeDefinition;
        $this->form = $form;
        $this->language = $language;
    }

    public function getAttributeDefinition(): AttributeDefinitionInterface
    {
        return $this->attributeDefinition;
    }

    public function setAttributeDefinition(AttributeDefinitionInterface $attributeDefinition): void
    {
        $this->attributeDefinition = $attributeDefinition;
    }

    public function getForm(): FormInterface
    {
        return $this->form;
    }

    public function setForm(FormInterface $form): void
    {
        $this->form = $form;
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
     * @return array<string,mixed>
     */
    protected function getInternalParameters(): array
    {
        return [
            'attribute_definition' => $this->attributeDefinition,
            'form' => $this->form->createView(),
            'language' => $this->language,
        ];
    }
}
