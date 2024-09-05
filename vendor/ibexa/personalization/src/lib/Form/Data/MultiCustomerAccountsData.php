<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Data;

final class MultiCustomerAccountsData
{
    private ?int $customerId;

    public function __construct(?int $customerId = null)
    {
        $this->customerId = $customerId;
    }

    public function getCustomerId(): ?int
    {
        return $this->customerId;
    }

    public function setCustomerId(?int $customerId): void
    {
        $this->customerId = $customerId;
    }
}

class_alias(MultiCustomerAccountsData::class, 'Ibexa\Platform\Personalization\Form\Data\MultiCustomerAccountsData');
