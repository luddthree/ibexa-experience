<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data;

use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeTypeInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class AttributeDefinitionPreCreateData
{
    /**
     * @Assert\NotBlank()
     */
    private ?Language $language;

    /**
     * @Assert\NotBlank()
     */
    private ?AttributeTypeInterface $attributeType;

    private ?AttributeGroupInterface $attributeGroup;

    public function __construct(
        ?Language $language = null,
        ?AttributeTypeInterface $attributeType = null,
        ?AttributeGroupInterface $attributeGroup = null
    ) {
        $this->language = $language;
        $this->attributeType = $attributeType;
        $this->attributeGroup = $attributeGroup;
    }

    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    public function setLanguage(?Language $language): void
    {
        $this->language = $language;
    }

    public function getAttributeType(): ?AttributeTypeInterface
    {
        return $this->attributeType;
    }

    public function setAttributeType(?AttributeTypeInterface $attributeType): void
    {
        $this->attributeType = $attributeType;
    }

    public function getAttributeGroup(): ?AttributeGroupInterface
    {
        return $this->attributeGroup;
    }

    public function setAttributeGroup(?AttributeGroupInterface $attributeGroup): void
    {
        $this->attributeGroup = $attributeGroup;
    }
}
