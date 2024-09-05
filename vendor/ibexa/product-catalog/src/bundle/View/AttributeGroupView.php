<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\View;

use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use Ibexa\Core\MVC\Symfony\View\BaseView;

final class AttributeGroupView extends BaseView
{
    private AttributeGroupInterface $attributeGroup;

    private bool $editable = false;

    public function __construct(
        $templateIdentifier,
        AttributeGroupInterface $attributeGroup
    ) {
        parent::__construct($templateIdentifier);

        $this->attributeGroup = $attributeGroup;
    }

    public function getAttributeGroup(): AttributeGroupInterface
    {
        return $this->attributeGroup;
    }

    public function setAttributeGroup(AttributeGroupInterface $attributeGroup): void
    {
        $this->attributeGroup = $attributeGroup;
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
            'attribute_group' => $this->attributeGroup,
            'is_editable' => $this->editable,
        ];
    }
}
