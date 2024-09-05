<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Decorator;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\CorporateAccount\Service\CompanyService as CompanyServiceInterface;
use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Contracts\CorporateAccount\Values\CompanyCreateStruct;
use Ibexa\Contracts\CorporateAccount\Values\CompanyUpdateStruct;
use Ibexa\Contracts\CorporateAccount\Values\Member;
use Ibexa\Contracts\CorporateAccount\Values\ShippingAddress;

abstract class CompanyService implements CompanyServiceInterface
{
    protected CompanyServiceInterface $innerService;

    public function __construct(
        CompanyServiceInterface $innerService
    ) {
        $this->innerService = $innerService;
    }

    public function getCompany(int $id): Company
    {
        return $this->innerService->getCompany($id);
    }

    public function getCompanies(
        ?Criterion $filter = null,
        array $sortClauses = [],
        ?int $limit = 25,
        int $offset = 0
    ): array {
        return $this->innerService->getCompanies($filter, $sortClauses, $limit, $offset);
    }

    public function getCompaniesCount(
        ?Criterion $filter = null
    ): int {
        return $this->innerService->getCompaniesCount($filter);
    }

    public function getCompanySalesRepresentative(Company $company): ?User
    {
        return $this->innerService->getCompanySalesRepresentative($company);
    }

    public function setDefaultShippingAddress(
        Company $company,
        ShippingAddress $shippingAddress
    ): void {
        $this->innerService->setDefaultShippingAddress($company, $shippingAddress);
    }

    public function setContact(
        Company $company,
        Member $contact
    ): void {
        $this->innerService->setContact($company, $contact);
    }

    public function setCompanyAddressBookRelation(
        Company $company,
        Content $content
    ): void {
        $this->innerService->setCompanyAddressBookRelation($company, $content);
    }

    public function setCompanyMembersRelation(
        Company $company,
        Content $content
    ): void {
        $this->innerService->setCompanyMembersRelation($company, $content);
    }

    public function createCompanyAddressBookFolder(Company $company): Content
    {
        return $this->innerService->createCompanyAddressBookFolder($company);
    }

    public function createCompanyMembersUserGroup(Company $company): Content
    {
        return $this->innerService->createCompanyMembersUserGroup($company);
    }

    public function createCompany(
        CompanyCreateStruct $companyCreateStruct,
        ?array $fieldIdentifiersToValidate = null
    ): Company {
        return $this->innerService->createCompany($companyCreateStruct, $fieldIdentifiersToValidate);
    }

    public function updateCompany(
        Company $company,
        CompanyUpdateStruct $companyUpdateStruct,
        ?array $fieldIdentifiersToValidate = null
    ): Company {
        return $this->innerService->updateCompany($company, $companyUpdateStruct, $fieldIdentifiersToValidate);
    }

    public function deleteCompany(Company $company): void
    {
        $this->innerService->deleteCompany($company);
    }

    public function newCompanyCreateStruct(): CompanyCreateStruct
    {
        return $this->innerService->newCompanyCreateStruct();
    }

    public function newCompanyUpdateStruct(): CompanyUpdateStruct
    {
        return $this->innerService->newCompanyUpdateStruct();
    }
}
