<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data\AttributeDefinition\Translation;

use Ibexa\Bundle\ProductCatalog\Form\Data\AbstractTranslationDeleteData;
use Ibexa\Bundle\ProductCatalog\Validator\Constraints\AtLeastOneLanguageWillRemain;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface;

/**
 * @AtLeastOneLanguageWillRemain
 */
final class TranslationDeleteData extends AbstractTranslationDeleteData
{
    /** @var (\Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface&\Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface)|null */
    private ?AttributeDefinitionInterface $attributeDefinition;

    /**
     * @phpstan-param (\Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface&\Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface)|null $attributeDefinition
     * @phpstan-param array<string, false>|null $languageCodes
     */
    public function __construct(
        ?AttributeDefinitionInterface $attributeDefinition = null,
        ?array $languageCodes = []
    ) {
        parent::__construct($languageCodes);

        $this->attributeDefinition = $attributeDefinition;
    }

    public function getAttributeDefinition(): ?AttributeDefinitionInterface
    {
        return $this->attributeDefinition;
    }

    /**
     * @phpstan-param (\Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface&\Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface)|null $attributeDefinition
     */
    public function setAttributeDefinition(?AttributeDefinitionInterface $attributeDefinition): void
    {
        $this->attributeDefinition = $attributeDefinition;
    }

    public function getTranslatable(): ?TranslatableInterface
    {
        return $this->attributeDefinition;
    }
}
