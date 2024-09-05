<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\View;

use Ibexa\Core\MVC\Symfony\View\BaseView;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Form\FormInterface;

class IndividualListView extends BaseView
{
    /** @var iterable<\Ibexa\CorporateAccount\View\IndividualListViewEntry> */
    private iterable $individuals;

    private FormInterface $searchForm;

    private Pagerfanta $pagination;

    /**
     * @param iterable<\Ibexa\CorporateAccount\View\IndividualListViewEntry> $individuals
     */
    public function __construct(
        string $templateIdentifier,
        iterable $individuals,
        FormInterface $searchForm,
        Pagerfanta $pagination
    ) {
        parent::__construct($templateIdentifier);

        $this->individuals = $individuals;
        $this->searchForm = $searchForm;
        $this->pagination = $pagination;
    }

    /**
     * @return array{
     *     individuals: iterable<\Ibexa\CorporateAccount\View\IndividualListViewEntry>,
     *     search_form: \Symfony\Component\Form\FormView,
     *     pagination: \Pagerfanta\Pagerfanta
     * }
     */
    protected function getInternalParameters()
    {
        return [
            'individuals' => $this->individuals,
            'search_form' => $this->searchForm->createView(),
            'pagination' => $this->pagination,
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

    /**
     * @return iterable<\Ibexa\CorporateAccount\View\IndividualListViewEntry>
     */
    public function getIndividuals(): iterable
    {
        return $this->individuals;
    }

    /**
     * @param iterable<\Ibexa\CorporateAccount\View\IndividualListViewEntry> $individuals
     */
    public function setIndividuals(iterable $individuals): void
    {
        $this->individuals = $individuals;
    }

    public function getPagination(): Pagerfanta
    {
        return $this->pagination;
    }

    public function setPagination(Pagerfanta $pagination): void
    {
        $this->pagination = $pagination;
    }
}
