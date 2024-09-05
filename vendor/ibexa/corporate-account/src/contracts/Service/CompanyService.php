<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CorporateAccount\Service;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Contracts\CorporateAccount\Values\CompanyCreateStruct;
use Ibexa\Contracts\CorporateAccount\Values\CompanyUpdateStruct;
use Ibexa\Contracts\CorporateAccount\Values\Member;
use Ibexa\Contracts\CorporateAccount\Values\ShippingAddress;

interface CompanyService
{
    public const DEFAULT_COMPANY_LIST_LIMIT = 25;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function getCompany(int $id): Company;

    public function getCompaniesCount(
        ?Criterion $filter = null
    ): int;

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause[] $sortClauses
     *
     * @return array<\Ibexa\Contracts\CorporateAccount\Values\Company>
     */
    public function getCompanies(
        ?Criterion $filter = null,
        array $sortClauses = [],
        ?int $limit = self::DEFAULT_COMPANY_LIST_LIMIT,
        int $offset = 0
    ): array;

    public function getCompanySalesRepresentative(Company $company): ?User;

    public function setDefaultShippingAddress(Company $company, ShippingAddress $shippingAddress): void;

    public function setContact(Company $company, Member $contact): void;

    public function setCompanyAddressBookRelation(Company $company, Content $content): void;

    public function setCompanyMembersRelation(Company $company, Content $content): void;

    public function createCompanyAddressBookFolder(Company $company): Content;

    public function createCompanyMembersUserGroup(Company $company): Content;

    /** @param string[]|null $fieldIdentifiersToValidate */
    public function createCompany(
        CompanyCreateStruct $companyCreateStruct,
        ?array $fieldIdentifiersToValidate = null
    ): Company;

    /** @param string[]|null $fieldIdentifiersToValidate */
    public function updateCompany(
        Company $company,
        CompanyUpdateStruct $companyUpdateStruct,
        ?array $fieldIdentifiersToValidate = null
    ): Company;

    public function deleteCompany(Company $company): void;

    public function newCompanyCreateStruct(): CompanyCreateStruct;

    public function newCompanyUpdateStruct(): CompanyUpdateStruct;
}
