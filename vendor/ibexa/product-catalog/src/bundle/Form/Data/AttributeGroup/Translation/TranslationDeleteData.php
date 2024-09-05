<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data\AttributeGroup\Translation;

use Ibexa\Bundle\ProductCatalog\Form\Data\AbstractTranslationDeleteData;
use Ibexa\Bundle\ProductCatalog\Validator\Constraints\AtLeastOneLanguageWillRemain;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface;

/**
 * @AtLeastOneLanguageWillRemain
 */
final class TranslationDeleteData extends AbstractTranslationDeleteData
{
    /**
     * @phpstan-var (\Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface&\Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface)|null
     */
    private ?AttributeGroupInterface $attributeGroup;

    /**
     * @phpstan-param (\Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface&\Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface)|null $attributeGroup
     * @phpstan-param array<string, false>|null $languageCodes
     */
    public function __construct(
        ?AttributeGroupInterface $attributeGroup = null,
        ?array $languageCodes = []
    ) {
        parent::__construct($languageCodes);

        $this->attributeGroup = $attributeGroup;
    }

    public function getAttributeGroup(): ?AttributeGroupInterface
    {
        return $this->attributeGroup;
    }

    /**
     * @param (\Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface&\Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface)|null $attributeGroup
     */
    public function setAttributeGroup(?AttributeGroupInterface $attributeGroup): void
    {
        $this->attributeGroup = $attributeGroup;
    }

    public function getTranslatable(): ?TranslatableInterface
    {
        return $this->attributeGroup;
    }
}
