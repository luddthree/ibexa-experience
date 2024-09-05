<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\Controller\REST;

use Ibexa\Contracts\CorporateAccount\Service\CompanyService;
use Ibexa\Contracts\CorporateAccount\Service\CorporateAccountService;
use Ibexa\Contracts\CorporateAccount\Values\Company as APICompany;
use Ibexa\CorporateAccount\REST\QueryBuilder\ContentQueryBuilderInterface;
use Ibexa\CorporateAccount\REST\Value\Company;
use Ibexa\CorporateAccount\REST\Value\CompanyCreateStruct;
use Ibexa\CorporateAccount\REST\Value\CompanyList;
use Ibexa\CorporateAccount\REST\Value\CompanyUpdateStruct;
use Ibexa\Rest\Server\Controller as RestController;
use Ibexa\Rest\Server\Values\NoContent;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
final class CompanyController extends RestController
{
    private CompanyService $companyService;

    private CorporateAccountService $corporateAccountService;

    private ContentQueryBuilderInterface $queryBuilder;

    public function __construct(
        CompanyService $companyService,
        CorporateAccountService $corporateAccountService,
        ContentQueryBuilderInterface $queryBuilder
    ) {
        $this->companyService = $companyService;
        $this->corporateAccountService = $corporateAccountService;
        $this->queryBuilder = $queryBuilder;
    }

    public function listCompaniesAction(Request $request): CompanyList
    {
        $query = $this->queryBuilder->buildQuery(
            $request,
            CompanyService::DEFAULT_COMPANY_LIST_LIMIT
        );

        return new CompanyList(
            array_map(
                static fn (APICompany $company) => new Company($company),
                $this->companyService->getCompanies(
                    $query->filter,
                    $query->sortClauses,
                    $query->limit,
                    $query->offset
                )
            )
        );
    }

    public function getCompanyAction(APICompany $company): Company
    {
        return new Company($company);
    }

    public function deleteCompanyAction(APICompany $company): NoContent
    {
        $this->companyService->deleteCompany($company);

        return new NoContent();
    }

    public function createCompanyAction(CompanyCreateStruct $companyCreateStruct): Company
    {
        $company = $this->corporateAccountService->createCompany(
            $companyCreateStruct->companyCreateStruct
        );

        return new Company($company);
    }

    public function updateCompanyAction(
        APICompany $company,
        CompanyUpdateStruct $companyUpdateStruct
    ): Company {
        $updatedCompany = $this->companyService->updateCompany(
            $company,
            $companyUpdateStruct->companyUpdateStruct
        );

        return new Company($updatedCompany);
    }
}
