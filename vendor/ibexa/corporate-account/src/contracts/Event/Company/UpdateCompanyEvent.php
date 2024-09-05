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

final class UpdateCompanyEvent extends Event
{
    private Company $updatedCompany;

    private Company $company;

    private CompanyUpdateStruct $companyUpdateStruct;

    /** @var string[]|null */
    private ?array $fieldIdentifiersToValidate;

    /**
     * @param string[]|null $fieldIdentifiersToValidate
     */
    public function __construct(
        Company $updatedCompany,
        Company $company,
        CompanyUpdateStruct $companyUpdateStruct,
        ?array $fieldIdentifiersToValidate = null
    ) {
        $this->updatedCompany = $updatedCompany;
        $this->company = $company;
        $this->companyUpdateStruct = $companyUpdateStruct;
        $this->fieldIdentifiersToValidate = $fieldIdentifiersToValidate;
    }

    public function getUpdatedCompany(): Company
    {
        return $this->updatedCompany;
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
}
