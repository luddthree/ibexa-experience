<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data\CustomerGroup\Translation;

use Ibexa\Bundle\ProductCatalog\Form\Data\AbstractTranslationDeleteData as BaseTranslationDeleteData;
use Ibexa\Bundle\ProductCatalog\Validator\Constraints\AtLeastOneLanguageWillRemain;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface;

/**
 * @AtLeastOneLanguageWillRemain
 */
final class TranslationDeleteData extends BaseTranslationDeleteData
{
    /** @var \Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface&\Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface */
    private CustomerGroupInterface $customerGroup;

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface&\Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface $customerGroup
     * @param array<string, false>|null $languageCodes
     */
    public function __construct(
        CustomerGroupInterface $customerGroup,
        ?array $languageCodes = []
    ) {
        parent::__construct($languageCodes);

        $this->customerGroup = $customerGroup;
    }

    /**
     * @return \Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface&\Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface
     */
    public function getCustomerGroup(): CustomerGroupInterface
    {
        return $this->customerGroup;
    }

    public function getTranslatable(): ?TranslatableInterface
    {
        return $this->customerGroup;
    }
}
