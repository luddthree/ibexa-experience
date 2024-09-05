<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\CustomerGroup;

use Ibexa\Bundle\ProductCatalog\Form\Data\SearchQueryData;
use Ibexa\Bundle\ProductCatalog\Form\Type\SearchType;
use Ibexa\Bundle\ProductCatalog\View\CustomerGroupListView;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\CustomerGroup\View;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion as BaseFieldValueCriterion;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\LogicalOr;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupQuery;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\Query\Criterion\CustomerGroupIdentifier;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\Query\Criterion\CustomerGroupName;
use Ibexa\ProductCatalog\Pagerfanta\Adapter\CustomerGroupListAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

final class ListController extends AbstractController
{
    private CustomerGroupServiceInterface $customerGroupService;

    private ConfigResolverInterface $configResolver;

    public function __construct(
        CustomerGroupServiceInterface $customerGroupService,
        ConfigResolverInterface $configResolver
    ) {
        $this->customerGroupService = $customerGroupService;
        $this->configResolver = $configResolver;
    }

    public function renderAction(Request $request): CustomerGroupListView
    {
        $this->denyAccessUnlessGranted(new View());

        $searchForm = $this->createSearchForm();
        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $data = $searchForm->getData();
            $query = $this->buildCustomerGroupQuery($data);
        }

        $customerGroups = new Pagerfanta(new CustomerGroupListAdapter($this->customerGroupService, $query ?? null));
        $customerGroups->setMaxPerPage($this->configResolver->getParameter('product_catalog.pagination.customer_groups_limit'));
        $customerGroups->setCurrentPage($request->query->getInt('page', 1));

        return new CustomerGroupListView('@ibexadesign/product_catalog/customer_group/list.html.twig', $customerGroups, $searchForm);
    }

    private function buildCustomerGroupQuery(SearchQueryData $data): CustomerGroupQuery
    {
        $criteria = null;
        if ($data->getQuery() !== null) {
            $criteria = new LogicalOr(
                new CustomerGroupName($data->getQuery(), BaseFieldValueCriterion::COMPARISON_STARTS_WITH),
                new CustomerGroupIdentifier($data->getQuery()),
            );
        }

        return new CustomerGroupQuery($criteria);
    }

    private function createSearchForm(): FormInterface
    {
        return $this->createForm(SearchType::class);
    }
}
