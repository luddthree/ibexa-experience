<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data\AttributeGroup\Translation;

use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class TranslationAddData
{
    /**
     * @Assert\NotBlank()
     */
    private ?AttributeGroupInterface $attributeGroup;

    /**
     * @Assert\NotBlank()
     */
    private ?Language $language;

    private ?Language $baseLanguage;

    public function __construct(
        ?AttributeGroupInterface $attributeGroup = null,
        ?Language $language = null,
        ?Language $baseLanguage = null
    ) {
        $this->attributeGroup = $attributeGroup;
        $this->language = $language;
        $this->baseLanguage = $baseLanguage;
    }

    public function getAttributeGroup(): ?AttributeGroupInterface
    {
        return $this->attributeGroup;
    }

    public function setAttributeGroup(AttributeGroupInterface $attributeGroup): self
    {
        $this->attributeGroup = $attributeGroup;

        return $this;
    }

    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    public function setLanguage(Language $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getBaseLanguage(): ?Language
    {
        return $this->baseLanguage;
    }

    public function setBaseLanguage(Language $baseLanguage): self
    {
        $this->baseLanguage = $baseLanguage;

        return $this;
    }
}
