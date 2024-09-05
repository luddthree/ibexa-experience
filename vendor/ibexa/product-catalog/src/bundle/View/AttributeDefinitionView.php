<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\View;

use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Ibexa\Core\MVC\Symfony\View\BaseView;

final class AttributeDefinitionView extends BaseView
{
    private AttributeDefinitionInterface $attributeDefinition;

    private bool $editable = false;

    public function __construct($templateIdentifier, AttributeDefinitionInterface $attributeDefinition)
    {
        parent::__construct($templateIdentifier);

        $this->attributeDefinition = $attributeDefinition;
    }

    public function getAttributeDefinition(): AttributeDefinitionInterface
    {
        return $this->attributeDefinition;
    }

    public function setAttributeDefinition(AttributeDefinitionInterface $attributeDefinition): void
    {
        $this->attributeDefinition = $attributeDefinition;
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
            'attribute_definition' => $this->attributeDefinition,
            'is_editable' => $this->editable,
        ];
    }
}
