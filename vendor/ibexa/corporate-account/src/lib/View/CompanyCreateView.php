<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\View;

use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Core\MVC\Symfony\View\BaseView;
use Symfony\Component\Form\FormInterface;

class CompanyCreateView extends BaseView
{
    private FormInterface $companyForm;

    private Location $parentLocation;

    private Language $language;

    /** @var array<string, array<int, string>> */
    private array $groupedFields;

    /**
     * @param array<string, array<int, string>> $groupedFields
     */
    public function __construct(
        string $templateIdentifier,
        FormInterface $companyForm,
        Location $parentLocation,
        Language $language,
        array $groupedFields
    ) {
        parent::__construct($templateIdentifier);

        $this->companyForm = $companyForm;
        $this->parentLocation = $parentLocation;
        $this->language = $language;
        $this->groupedFields = $groupedFields;
    }

    /**
     * @return array{
     *     company_form: \Symfony\Component\Form\FormView,
     *     grouped_fields: array<string, array<int, string>>
     * }
     */
    protected function getInternalParameters()
    {
        return [
            'company_form' => $this->companyForm->createView(),
            'grouped_fields' => $this->groupedFields,
        ];
    }

    public function getCompanyForm(): FormInterface
    {
        return $this->companyForm;
    }

    public function setCompanyForm(FormInterface $companyForm): void
    {
        $this->companyForm = $companyForm;
    }

    public function getParentLocation(): Location
    {
        return $this->parentLocation;
    }

    public function setParentLocation(Location $parentLocation): void
    {
        $this->parentLocation = $parentLocation;
    }

    public function getLanguage(): Language
    {
        return $this->language;
    }

    public function setLanguage(Language $language): void
    {
        $this->language = $language;
    }

    /** @return array<string, array<int, string>> */
    public function getGroupedFields(): array
    {
        return $this->groupedFields;
    }

    /** @param array<string, array<int, string>> $groupedFields */
    public function setGroupedFields(array $groupedFields): void
    {
        $this->groupedFields = $groupedFields;
    }
}
