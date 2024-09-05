<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data\AttributeDefinition\Translation;

use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class TranslationAddData
{
    /**
     * @Assert\NotBlank()
     */
    private ?AttributeDefinitionInterface $attributeDefinition;

    /**
     * @Assert\NotBlank()
     */
    private ?Language $language;

    private ?Language $baseLanguage;

    public function __construct(
        ?AttributeDefinitionInterface $attributeDefinition = null,
        ?Language $language = null,
        ?Language $baseLanguage = null
    ) {
        $this->attributeDefinition = $attributeDefinition;
        $this->language = $language;
        $this->baseLanguage = $baseLanguage;
    }

    public function getAttributeDefinition(): ?AttributeDefinitionInterface
    {
        return $this->attributeDefinition;
    }

    public function setAttributeDefinition(AttributeDefinitionInterface $attributeDefinition): self
    {
        $this->attributeDefinition = $attributeDefinition;

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
