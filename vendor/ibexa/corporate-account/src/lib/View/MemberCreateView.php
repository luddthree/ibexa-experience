<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\View;

use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\User\UserGroup;
use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Core\MVC\Symfony\View\BaseView;
use Symfony\Component\Form\FormInterface;

class MemberCreateView extends BaseView
{
    private FormInterface $memberForm;

    private UserGroup $membersGroup;

    private Language $language;

    private Company $company;

    public function __construct(
        string $templateIdentifier,
        FormInterface $memberForm,
        UserGroup $membersGroup,
        Language $language,
        Company $company
    ) {
        parent::__construct($templateIdentifier);

        $this->memberForm = $memberForm;
        $this->membersGroup = $membersGroup;
        $this->language = $language;
        $this->company = $company;
    }

    /**
     * @return array{
     *     shipping_address_form: \Symfony\Component\Form\FormView
     * }
     */
    protected function getInternalParameters()
    {
        return [
            'shipping_address_form' => $this->memberForm->createView(),
        ];
    }

    public function getMemberForm(): FormInterface
    {
        return $this->memberForm;
    }

    public function setMemberForm(FormInterface $memberForm): void
    {
        $this->memberForm = $memberForm;
    }

    public function getMembersGroup(): UserGroup
    {
        return $this->membersGroup;
    }

    public function setMembersGroup(UserGroup $membersGroup): void
    {
        $this->membersGroup = $membersGroup;
    }

    public function getLanguage(): Language
    {
        return $this->language;
    }

    public function setLanguage(Language $language): void
    {
        $this->language = $language;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function setCompany(Company $company): void
    {
        $this->company = $company;
    }
}
