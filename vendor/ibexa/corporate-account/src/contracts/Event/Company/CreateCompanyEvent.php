<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CorporateAccount\Event\Company;

use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Contracts\CorporateAccount\Values\CompanyCreateStruct;
use Symfony\Contracts\EventDispatcher\Event;

final class CreateCompanyEvent extends Event
{
    private Company $company;

    private CompanyCreateStruct $companyCreateStruct;

    /** @var string[]|null */
    private ?array $fieldIdentifiersToValidate;

    /**
     * @param string[]|null $fieldIdentifiersToValidate
     */
    public function __construct(
        Company $company,
        CompanyCreateStruct $companyCreateStruct,
        ?array $fieldIdentifiersToValidate = null
    ) {
        $this->company = $company;
        $this->companyCreateStruct = $companyCreateStruct;
        $this->fieldIdentifiersToValidate = $fieldIdentifiersToValidate;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function getCompanyCreateStruct(): CompanyCreateStruct
    {
        return $this->companyCreateStruct;
    }

    /**
     * @return string[]|null
     */
    public function getFieldIdentifiersToValidate(): ?array
    {
        return $this->fieldIdentifiersToValidate;
    }
}
