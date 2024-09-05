<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\View;

use Ibexa\Core\MVC\Symfony\View\BaseView;
use Symfony\Component\Form\FormInterface;

final class CompanyListView extends BaseView
{
    private FormInterface $searchForm;

    /** @var iterable<int, \Ibexa\Contracts\CorporateAccount\Values\Company> */
    private iterable $companies;

    /** @var array<int, \Ibexa\Contracts\Core\Repository\Values\User\User> */
    private array $salesReps;

    /** @var array<int, \Ibexa\Contracts\Core\Repository\Values\User\User> */
    private array $contacts;

    /**
     * @param iterable<int, \Ibexa\Contracts\CorporateAccount\Values\Company> $companies
     * @param array<int, \Ibexa\Contracts\Core\Repository\Values\User\User> $salesReps
     * @param array<int, \Ibexa\Contracts\Core\Repository\Values\User\User> $contacts
     */
    public function __construct(
        string $templateIdentifier,
        iterable $companies,
        FormInterface $searchForm,
        array $salesReps,
        array $contacts
    ) {
        parent::__construct($templateIdentifier);

        $this->searchForm = $searchForm;
        $this->companies = $companies;
        $this->salesReps = $salesReps;
        $this->contacts = $contacts;
    }

    /**
     * @return array{
     *     companies: iterable<int, \Ibexa\Contracts\CorporateAccount\Values\Company>,
     *     sales_reps: array<int, \Ibexa\Contracts\Core\Repository\Values\User\User>,
     *     contacts: array<int, \Ibexa\Contracts\Core\Repository\Values\User\User>,
     *     search_form: \Symfony\Component\Form\FormView
     * }
     */
    protected function getInternalParameters(): array
    {
        return [
            'companies' => $this->companies,
            'sales_reps' => $this->salesReps,
            'contacts' => $this->contacts,
            'search_form' => $this->searchForm->createView(),
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

    /** @return iterable<int, \Ibexa\Contracts\CorporateAccount\Values\Company> */
    public function getCompanies(): iterable
    {
        return $this->companies;
    }

    /** @param iterable<int, \Ibexa\Contracts\CorporateAccount\Values\Company> $companies */
    public function setCompanies(iterable $companies): void
    {
        $this->companies = $companies;
    }

    /** @return array<int, \Ibexa\Contracts\Core\Repository\Values\User\User> */
    public function getSalesReps(): array
    {
        return $this->salesReps;
    }

    /** @param array<int, \Ibexa\Contracts\Core\Repository\Values\User\User> $salesReps */
    public function setSalesReps(array $salesReps): void
    {
        $this->salesReps = $salesReps;
    }

    /** @return array<int, \Ibexa\Contracts\Core\Repository\Values\User\User> */
    public function getContacts(): array
    {
        return $this->contacts;
    }

    /** @param array<int, \Ibexa\Contracts\Core\Repository\Values\User\User> $contacts */
    public function setContacts(array $contacts): void
    {
        $this->contacts = $contacts;
    }
}
