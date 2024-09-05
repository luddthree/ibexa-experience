<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\View;

use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Core\MVC\Symfony\View\BaseView;
use Symfony\Component\Form\FormInterface;

class CompanyEditView extends BaseView
{
    private Company $company;

    private FormInterface $companyForm;

    public function __construct(
        string $templateIdentifier,
        Company $company,
        FormInterface $companyForm
    ) {
        parent::__construct($templateIdentifier);

        $this->company = $company;
        $this->companyForm = $companyForm;
    }

    /**
     * @return array{
     *     company: \Ibexa\Contracts\CorporateAccount\Values\Company,
     *     company_form: \Symfony\Component\Form\FormView
     * }
     */
    protected function getInternalParameters()
    {
        return [
            'company' => $this->company,
            'company_form' => $this->companyForm->createView(),
        ];
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function setCompany(Company $company): void
    {
        $this->company = $company;
    }

    public function getCompanyForm(): FormInterface
    {
        return $this->companyForm;
    }

    public function setCompanyForm(FormInterface $companyForm): void
    {
        $this->companyForm = $companyForm;
    }
}
