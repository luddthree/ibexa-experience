<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\CustomerGroup;

use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;

final class CustomerGroupDeleteTranslationStruct
{
    private CustomerGroupInterface $customerGroup;

    private string $languageCode;

    public function __construct(CustomerGroupInterface $customerGroup, string $languageCode)
    {
        $this->customerGroup = $customerGroup;
        $this->languageCode = $languageCode;
    }

    public function getCustomerGroup(): CustomerGroupInterface
    {
        return $this->customerGroup;
    }

    public function getLanguageCode(): string
    {
        return $this->languageCode;
    }
}
