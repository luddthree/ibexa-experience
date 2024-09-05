<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Form\Data;

class CompanySearchQueryData
{
    private ?string $query;

    private ?bool $status;

    private ?int $customerGroup;

    public function __construct(
        ?string $query = null,
        ?bool $status = null,
        ?int $customerGroup = null
    ) {
        $this->query = $query;
        $this->status = $status;
        $this->customerGroup = $customerGroup;
    }

    public function getQuery(): ?string
    {
        return $this->query;
    }

    public function setQuery(?string $query): void
    {
        $this->query = $query;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(?bool $status): void
    {
        $this->status = $status;
    }

    public function getCustomerGroup(): ?int
    {
        return $this->customerGroup;
    }

    public function setCustomerGroup(?int $customerGroup): void
    {
        $this->customerGroup = $customerGroup;
    }
}
