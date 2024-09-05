<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\View;

use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use Ibexa\Core\MVC\Symfony\View\BaseView;
use Symfony\Component\Form\FormInterface;

final class AttributeGroupUpdateView extends BaseView
{
    private AttributeGroupInterface $attributeGroup;

    private FormInterface $form;

    public function __construct($templateIdentifier, AttributeGroupInterface $attributeGroup, FormInterface $form)
    {
        parent::__construct($templateIdentifier);

        $this->attributeGroup = $attributeGroup;
        $this->form = $form;
    }

    public function getAttributeGroup(): AttributeGroupInterface
    {
        return $this->attributeGroup;
    }

    public function setAttributeGroup(AttributeGroupInterface $attributeGroup): void
    {
        $this->attributeGroup = $attributeGroup;
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
            'attribute_group' => $this->attributeGroup,
            'form' => $this->form->createView(),
        ];
    }
}
