<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CorporateAccount\Event\Company;

use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Contracts\CorporateAccount\Values\CompanyUpdateStruct;
use Symfony\Contracts\EventDispatcher\Event;
use UnexpectedValueException;

final class BeforeUpdateCompanyEvent extends Event
{
    private Company $company;

    private CompanyUpdateStruct $companyUpdateStruct;

    /** @var string[]|null */
    private ?array $fieldIdentifiersToValidate;

    private ?Company $updatedCompany = null;

    /**
     * @param string[]|null $fieldIdentifiersToValidate
     */
    public function __construct(
        Company $company,
        CompanyUpdateStruct $companyUpdateStruct,
        ?array $fieldIdentifiersToValidate = null
    ) {
        $this->company = $company;
        $this->companyUpdateStruct = $companyUpdateStruct;
        $this->fieldIdentifiersToValidate = $fieldIdentifiersToValidate;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function getCompanyUpdateStruct(): CompanyUpdateStruct
    {
        return $this->companyUpdateStruct;
    }

    /**
     * @return string[]|null
     */
    public function getFieldIdentifiersToValidate(): ?array
    {
        return $this->fieldIdentifiersToValidate;
    }

    public function getUpdatedCompany(): Company
    {
        if (!$this->hasUpdatedCompany()) {
            throw new UnexpectedValueException(sprintf('Return value is not set or not of type %s. Check hasUpdatedCompany() or set it using setUpdatedCompany() before you call the getter.', Company::class));
        }

        return $this->updatedCompany;
    }

    public function setUpdatedCompany(?Company $updatedCompany): void
    {
        $this->updatedCompany = $updatedCompany;
    }

    public function hasUpdatedCompany(): bool
    {
        return $this->updatedCompany instanceof Company;
    }
}
