<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\Controller;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\CorporateAccount\Service\ApplicationService;
use Ibexa\Contracts\CorporateAccount\Values\Query\Criterion\ApplicationName;
use Ibexa\Contracts\CorporateAccount\Values\Query\Criterion\ApplicationState;
use Ibexa\Core\MVC\Symfony\View\BaseView;
use Ibexa\CorporateAccount\Configuration\CorporateAccountConfiguration;
use Ibexa\CorporateAccount\Form\ApplicationFormFactory;
use Ibexa\CorporateAccount\Form\Data\Application\ApplicationsDeleteData;
use Ibexa\CorporateAccount\Form\Data\Application\ApplicationSearchQueryData;
use Ibexa\CorporateAccount\Pagerfanta\Adapter\ApplicationListAdapter;
use Ibexa\CorporateAccount\View\ApplicationListView;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;

class ApplicationListController extends Controller
{
    private ApplicationFormFactory $formFactory;

    private ConfigResolverInterface $configResolver;

    private ApplicationService $applicationService;

    private ContentService $contentService;

    public function __construct(
        CorporateAccountConfiguration $corporateAccount,
        ApplicationFormFactory $formFactory,
        ApplicationService $applicationService,
        ContentService $contentService,
        ConfigResolverInterface $configResolver
    ) {
        parent::__construct($corporateAccount);
        $this->formFactory = $formFactory;
        $this->configResolver = $configResolver;
        $this->applicationService = $applicationService;
        $this->contentService = $contentService;
    }

    public function listAction(Request $request): BaseView
    {
        $searchForm = $this->formFactory->getSearchForm();

        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $data = $searchForm->getData();
            $criteria = $this->buildApplicationCriteria($data);
        }

        $applications = new Pagerfanta(
            new ApplicationListAdapter(
                $this->applicationService,
                empty($criteria) ? new Criterion\MatchAll() : new Criterion\LogicalAnd($criteria)
            )
        );

        $applications->setMaxPerPage($this->configResolver->getParameter('corporate_account.pagination.applications_limit'));
        $applications->setCurrentPage($request->query->getInt('page', 1));

        $rawApplications = iterator_to_array($applications);
        $applicationStates = $this->applicationService->getApplicationsStates(
            $rawApplications
        );

        $deleteApplicationsForm = $this->formFactory->getDeleteApplicationsForm(
            new ApplicationsDeleteData($this->getApplicationsData($rawApplications))
        );

        $salesReps = [];
        /** @var \Ibexa\Contracts\CorporateAccount\Values\Application $application */
        foreach ($rawApplications as $application) {
            $salesRepField = $application->getContent()->getField('sales_rep');
            if (null === $salesRepField) {
                continue;
            }
            $contentId = $salesRepField->value->destinationContentId;
            if (null !== $contentId) {
                $contentId = (int)$contentId;
                $salesReps[$contentId] ??= $this->contentService->loadContentInfo($contentId);
            }
        }

        $applicationStatesByApplicationId = [];
        foreach ($applicationStates as $applicationState) {
            $applicationStatesByApplicationId[$applicationState->getApplicationId()] = $applicationState;
        }

        return new ApplicationListView(
            '@ibexadesign/corporate_account/application/list.html.twig',
            $applications,
            $applicationStatesByApplicationId,
            $salesReps,
            $searchForm,
            $deleteApplicationsForm
        );
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion[]
     */
    private function buildApplicationCriteria(ApplicationSearchQueryData $data): array
    {
        $criteria = [];

        if ($data->getQuery() !== null) {
            $criteria[] = new ApplicationName(Operator::CONTAINS, $data->getQuery());
        }

        if ($data->getState() !== null) {
            $criteria[] = new ApplicationState(Operator::EQ, $data->getState());
        }

        return $criteria;
    }

    /**
     * @param \Ibexa\Contracts\CorporateAccount\Values\Application[] $applications
     *
     * @return array<int, bool>
     */
    private function getApplicationsData(array $applications): array
    {
        $ids = [];

        foreach ($applications as $application) {
            $ids[$application->getId()] = false;
        }

        return $ids;
    }
}
