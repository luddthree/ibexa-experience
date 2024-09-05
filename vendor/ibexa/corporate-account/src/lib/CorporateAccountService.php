<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount;

use Ibexa\Contracts\CorporateAccount\Service\CompanyService as CompanyServiceInterface;
use Ibexa\Contracts\CorporateAccount\Service\CorporateAccountService as CorporateAccountServiceInterface;
use Ibexa\Contracts\CorporateAccount\Service\ShippingAddressService as ShippingAddressServiceInterface;
use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Contracts\CorporateAccount\Values\CompanyCreateStruct;

/**
 * @internal
 */
final class CorporateAccountService implements CorporateAccountServiceInterface
{
    private CompanyServiceInterface $companyService;

    private ShippingAddressServiceInterface $shippingAddressService;

    public function __construct(
        CompanyServiceInterface $companyService,
        ShippingAddressServiceInterface $shippingAddressService
    ) {
        $this->companyService = $companyService;
        $this->shippingAddressService = $shippingAddressService;
    }

    public function createCompany(CompanyCreateStruct $companyCreateStruct): Company
    {
        $company = $this->companyService->createCompany($companyCreateStruct);

        $this->companyService->createCompanyMembersUserGroup($company);
        $this->companyService->createCompanyAddressBookFolder($company);

        $company = $this->companyService->getCompany($company->getId());

        $shippingAddress = $this->shippingAddressService->createShippingAddressFromCompanyBillingAddress(
            $company
        );

        $this->companyService->setDefaultShippingAddress($company, $shippingAddress);

        // reload Company so the API object reflects the changes
        return $this->companyService->getCompany($company->getId());
    }
}
