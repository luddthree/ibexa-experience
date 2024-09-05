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
use UnexpectedValueException;

final class BeforeCreateCompanyEvent extends Event
{
    private CompanyCreateStruct $companyCreateStruct;

    /** @var string[]|null */
    private ?array $fieldIdentifiersToValidate;

    private ?Company $company = null;

    /** @param string[]|null $fieldIdentifiersToValidate  */
    public function __construct(
        CompanyCreateStruct $companyCreateStruct,
        ?array $fieldIdentifiersToValidate = null
    ) {
        $this->companyCreateStruct = $companyCreateStruct;
        $this->fieldIdentifiersToValidate = $fieldIdentifiersToValidate;
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

    public function getCompany(): Company
    {
        if (!$this->hasCompany()) {
            throw new UnexpectedValueException(sprintf('Return value is not set or not of type %s. Check hasCompany() or set it using setCompany() before you call the getter.', Company::class));
        }

        return $this->company;
    }

    public function setCompany(?Company $company): void
    {
        $this->company = $company;
    }

    public function hasCompany(): bool
    {
        return $this->company instanceof Company;
    }
}
