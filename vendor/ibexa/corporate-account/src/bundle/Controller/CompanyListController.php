<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\Controller;

use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\CorporateAccount\Service\CompanyService;
use Ibexa\Contracts\CorporateAccount\Values\Query\Criterion\CompanyName;
use Ibexa\Contracts\CorporateAccount\Values\Query\Criterion\CompanyStatus;
use Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\CustomerGroupId;
use Ibexa\CorporateAccount\Configuration\CorporateAccountConfiguration;
use Ibexa\CorporateAccount\Form\CompanyFormFactory;
use Ibexa\CorporateAccount\Form\Data\CompanySearchQueryData;
use Ibexa\CorporateAccount\Pagerfanta\Adapter\CompanyListAdapter;
use Ibexa\CorporateAccount\View\CompanyListView;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;

final class CompanyListController extends Controller
{
    private CompanyFormFactory $formFactory;

    private ConfigResolverInterface $configResolver;

    private CompanyService $companyService;

    private UserService $userService;

    public function __construct(
        CorporateAccountConfiguration $corporateAccount,
        CompanyService $companyService,
        CompanyFormFactory $formFactory,
        ConfigResolverInterface $configResolver,
        UserService $userService
    ) {
        parent::__construct($corporateAccount);

        $this->formFactory = $formFactory;
        $this->configResolver = $configResolver;
        $this->companyService = $companyService;
        $this->userService = $userService;
    }

    public function listAction(Request $request): CompanyListView
    {
        $searchForm = $this->formFactory->getSearchForm();

        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $data = $searchForm->getData();
            $criteria = $this->buildCompanyCriteria($data);
        }

        $companies = new Pagerfanta(
            new CompanyListAdapter(
                $this->companyService,
                empty($criteria) ? new Criterion\MatchAll() : new Criterion\LogicalAnd($criteria)
            )
        );

        $companies->setMaxPerPage($this->configResolver->getParameter('corporate_account.pagination.companies_limit'));
        $companies->setCurrentPage($request->query->getInt('page', 1));

        $rawCompanies = iterator_to_array($companies);

        $salesReps = $this->getCompaniesUsersRelations($rawCompanies, 'sales_rep');
        $contacts = $this->getCompaniesUsersRelations($rawCompanies, 'contact');

        return new CompanyListView(
            '@ibexadesign/corporate_account/company/list.html.twig',
            $companies,
            $searchForm,
            $salesReps,
            $contacts
        );
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion[]
     */
    private function buildCompanyCriteria(CompanySearchQueryData $data): array
    {
        $criteria = [];

        if ($data->getQuery() !== null) {
            $criteria[] = new CompanyName(Operator::CONTAINS, $data->getQuery());
        }

        if ($data->getStatus() !== null) {
            $criteria[] = new CompanyStatus(Operator::EQ, $data->getStatus());
        }

        if ($data->getCustomerGroup() !== null) {
            $criteria[] = new CustomerGroupId($data->getCustomerGroup());
        }

        return $criteria;
    }

    /**
     * @param \Ibexa\Contracts\CorporateAccount\Values\Company[] $companies
     *
     * @return array<int, \Ibexa\Contracts\Core\Repository\Values\User\User>
     */
    private function getCompaniesUsersRelations(
        array $companies,
        string $fieldIdentifier
    ): array {
        $results = [];
        foreach ($companies as $company) {
            $salesRepField = $company->getContent()->getField($fieldIdentifier);
            if (null === $salesRepField) {
                continue;
            }

            $contentId = $salesRepField->value->destinationContentId;
            if (null !== $contentId) {
                $contentId = (int)$contentId;
                if (isset($results[$contentId])) {
                    continue;
                }

                $results[$contentId] = $this->userService->loadUser($contentId);
            }
        }

        return $results;
    }
}
