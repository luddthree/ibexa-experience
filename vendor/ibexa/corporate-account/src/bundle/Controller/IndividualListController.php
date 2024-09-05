<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\Controller;

use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\ProductCatalog\CustomerGroupResolverInterface;
use Ibexa\Core\MVC\Symfony\View\BaseView;
use Ibexa\CorporateAccount\Commerce\Individuals\IndividualsLocationResolverInterface;
use Ibexa\CorporateAccount\Configuration\CorporateAccountConfiguration;
use Ibexa\CorporateAccount\Form\MemberFormFactory;
use Ibexa\CorporateAccount\Pagerfanta\Adapter\IndividualListAdapter;
use Ibexa\CorporateAccount\View\IndividualListView;
use Ibexa\CorporateAccount\View\IndividualListViewEntry;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;

class IndividualListController extends Controller
{
    private MemberFormFactory $formFactory;

    private SearchService $searchService;

    private UserService $userService;

    private ConfigResolverInterface $configResolver;

    private CustomerGroupResolverInterface $customerGroupResolver;

    private IndividualsLocationResolverInterface $individualsLocationResolver;

    public function __construct(
        CorporateAccountConfiguration $corporateAccount,
        MemberFormFactory $formFactory,
        SearchService $searchService,
        UserService $userProvider,
        CustomerGroupResolverInterface $customerGroupResolver,
        ConfigResolverInterface $configResolver,
        IndividualsLocationResolverInterface $individualsLocationResolver
    ) {
        parent::__construct($corporateAccount);
        $this->formFactory = $formFactory;
        $this->searchService = $searchService;
        $this->userService = $userProvider;
        $this->configResolver = $configResolver;
        $this->customerGroupResolver = $customerGroupResolver;
        $this->individualsLocationResolver = $individualsLocationResolver;
    }

    public function listAction(Request $request): BaseView
    {
        $criterions = [
            new Criterion\Subtree($this->individualsLocationResolver->resolveLocation()->pathString),
        ];

        $searchForm = $this->formFactory->getIndividualSearchForm();
        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            /** @var \Ibexa\CorporateAccount\Form\Data\CompanySearchQueryData $data */
            $data = $searchForm->getData();
            if ($data->getQuery() !== null) {
                $criterions[] = new Criterion\FullText($searchForm->getData()->getQuery());
            }
        }

        $pagination = new Pagerfanta(
            new IndividualListAdapter(
                $this->searchService,
                $this->corporateAccount,
                $criterions,
                $this->configResolver,
                $this->userService
            )
        );

        $pagination->setMaxPerPage($this->configResolver->getParameter('corporate_account.pagination.individuals_limit'));
        $pagination->setCurrentPage($request->query->getInt('page', 1));

        return new IndividualListView(
            '@ibexadesign/corporate_account/individual/list.html.twig',
            $this->createIndividualListViewEntries($pagination),
            $searchForm,
            $pagination
        );
    }

    /**
     * @param iterable<\Ibexa\Contracts\Core\Repository\Values\User\User> $individuals
     *
     * @return iterable<\Ibexa\CorporateAccount\View\IndividualListViewEntry>
     */
    private function createIndividualListViewEntries(iterable $individuals): iterable
    {
        foreach ($individuals as $individual) {
            yield new IndividualListViewEntry(
                $individual,
                $this->customerGroupResolver->resolveCustomerGroup($individual)
            );
        }
    }
}
