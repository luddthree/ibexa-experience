<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\View;

use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Contracts\CorporateAccount\Values\Member;
use Ibexa\Core\MVC\Symfony\View\BaseView;
use Symfony\Component\Form\FormInterface;

class MemberEditView extends BaseView
{
    private Member $member;

    private Company $company;

    private FormInterface $memberForm;

    public function __construct(
        string $templateIdentifier,
        Member $member,
        Company $company,
        FormInterface $memberForm
    ) {
        parent::__construct($templateIdentifier);

        $this->member = $member;
        $this->memberForm = $memberForm;
        $this->company = $company;
    }

    /**
     * @return array{
     *     member: \Ibexa\Contracts\CorporateAccount\Values\Member,
     *     member_form: \Symfony\Component\Form\FormView
     * }
     */
    protected function getInternalParameters()
    {
        return [
            'member' => $this->member,
            'member_form' => $this->memberForm->createView(),
        ];
    }

    public function getMember(): Member
    {
        return $this->member;
    }

    public function setMember(Member $member): void
    {
        $this->member = $member;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function setCompany(Company $company): void
    {
        $this->company = $company;
    }

    public function getMemberForm(): FormInterface
    {
        return $this->memberForm;
    }

    public function setMemberForm(FormInterface $memberForm): void
    {
        $this->memberForm = $memberForm;
    }
}
