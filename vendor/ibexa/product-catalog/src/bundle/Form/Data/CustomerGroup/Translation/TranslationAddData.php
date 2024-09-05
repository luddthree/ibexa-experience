<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data\CustomerGroup\Translation;

use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class TranslationAddData
{
    /**
     * @var \Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface&\Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface
     */
    private CustomerGroupInterface $customerGroup;

    /**
     * @Assert\NotBlank()
     */
    private ?Language $language;

    private ?Language $baseLanguage;

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface&\Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface $customerGroup
     */
    public function __construct(
        CustomerGroupInterface $customerGroup,
        ?Language $language = null,
        ?Language $baseLanguage = null
    ) {
        $this->customerGroup = $customerGroup;
        $this->language = $language;
        $this->baseLanguage = $baseLanguage;
    }

    /**
     * @return \Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface&\Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface
     */
    public function getCustomerGroup(): CustomerGroupInterface
    {
        return $this->customerGroup;
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
