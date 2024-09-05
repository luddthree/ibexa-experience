<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\View;

use Ibexa\Core\MVC\Symfony\View\BaseView;
use Symfony\Component\Form\FormInterface;

class ApplicationListView extends BaseView
{
    private FormInterface $searchForm;

    private FormInterface $deleteApplicationsForm;

    /** @var iterable<int, \Ibexa\Contracts\CorporateAccount\Values\Application> */
    private iterable $applications;

    /** @var iterable<int, \Ibexa\Contracts\CorporateAccount\Values\ApplicationState> */
    private iterable $applicationStates;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo[]|iterable */
    private iterable $salesReps;

    /**
     * @param iterable<int, \Ibexa\Contracts\CorporateAccount\Values\Application> $applications
     * @param iterable<int, \Ibexa\Contracts\CorporateAccount\Values\ApplicationState> $applicationStates
     * @param iterable<int, \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo> $salesReps
     */
    public function __construct(
        string $templateIdentifier,
        iterable $applications,
        iterable $applicationStates,
        iterable $salesReps,
        FormInterface $searchForm,
        FormInterface $deleteApplicationsForm
    ) {
        parent::__construct($templateIdentifier);

        $this->searchForm = $searchForm;
        $this->applications = $applications;
        $this->applicationStates = $applicationStates;
        $this->salesReps = $salesReps;
        $this->deleteApplicationsForm = $deleteApplicationsForm;
    }

    /**
     * @return array{
     *     applications: iterable<int, \Ibexa\Contracts\CorporateAccount\Values\Application>,
     *     application_states: iterable<int, \Ibexa\Contracts\CorporateAccount\Values\ApplicationState>,
     *     sales_reps: iterable<int, \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo>,
     *     search_form: \Symfony\Component\Form\FormView
     * }
     */
    protected function getInternalParameters(): array
    {
        return [
            'applications' => $this->applications,
            'application_states' => $this->applicationStates,
            'sales_reps' => $this->salesReps,
            'search_form' => $this->searchForm->createView(),
            'delete_applications_form' => $this->deleteApplicationsForm->createView(),
        ];
    }

    public function getSearchForm(): FormInterface
    {
        return $this->searchForm;
    }

    public function setSearchForm(FormInterface $searchForm): void
    {
        $this->searchForm = $searchForm;
    }

    public function getDeleteApplicationsForm(): FormInterface
    {
        return $this->deleteApplicationsForm;
    }

    /** @return iterable<int, \Ibexa\Contracts\CorporateAccount\Values\Application> */
    public function getApplications(): iterable
    {
        return $this->applications;
    }

    /** @return iterable<int, \Ibexa\Contracts\CorporateAccount\Values\ApplicationState> */
    public function getApplicationStates(): iterable
    {
        return $this->applicationStates;
    }

    /** @param iterable<int, \Ibexa\Contracts\CorporateAccount\Values\Application> $applications */
    public function setApplications(iterable $applications): void
    {
        $this->applications = $applications;
    }
}
