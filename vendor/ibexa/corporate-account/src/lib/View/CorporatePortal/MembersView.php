<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\View\CorporatePortal;

use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Core\MVC\Symfony\View\BaseView;
use Symfony\Component\Form\FormInterface;

class MembersView extends BaseView
{
    private Company $company;

    /** @var iterable<\Ibexa\Contracts\CorporateAccount\Values\Member> */
    private iterable $members;

    /** @var iterable<\Ibexa\Contracts\User\Invitation\Invitation> */
    private iterable $invitations;

    private FormInterface $membersSearchForm;

    private FormInterface $changeRoleForm;

    private FormInterface $invitationForm;

    private FormInterface $invitationResendForm;

    private FormInterface $invitationReinviteForm;

    /**
     * @param iterable<\Ibexa\Contracts\CorporateAccount\Values\Member> $members
     * @param iterable<\Ibexa\Contracts\User\Invitation\Invitation> $invitations
     */
    public function __construct(
        string $templateIdentifier,
        Company $company,
        iterable $members,
        iterable $invitations,
        FormInterface $membersSearchForm,
        FormInterface $changeRoleForm,
        FormInterface $invitationForm,
        FormInterface $invitationResendForm,
        FormInterface $invitationReinviteForm
    ) {
        parent::__construct($templateIdentifier);

        $this->company = $company;
        $this->members = $members;
        $this->invitations = $invitations;
        $this->membersSearchForm = $membersSearchForm;
        $this->changeRoleForm = $changeRoleForm;
        $this->invitationForm = $invitationForm;
        $this->invitationResendForm = $invitationResendForm;
        $this->invitationReinviteForm = $invitationReinviteForm;
    }

    /**
     * @return array{
     *     company: \Ibexa\Contracts\CorporateAccount\Values\Company,
     *     members: iterable<\Ibexa\Contracts\CorporateAccount\Values\Member>,
     *     invitations: iterable<\Ibexa\Contracts\User\Invitation\Invitation>,
     *     members_search_form: \Symfony\Component\Form\FormView,
     *     change_role_form: \Symfony\Component\Form\FormView
     * }
     */
    protected function getInternalParameters()
    {
        return [
            'company' => $this->company,
            'members' => $this->members,
            'invitations' => $this->invitations,
            'members_search_form' => $this->membersSearchForm->createView(),
            'change_role_form' => $this->changeRoleForm->createView(),
            'invitation_form' => $this->invitationForm->createView(),
            'invitation_resend_form' => $this->invitationResendForm->createView(),
            'invitation_reinvite_form' => $this->invitationReinviteForm->createView(),
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

    /** @return iterable<\Ibexa\Contracts\CorporateAccount\Values\Member> */
    public function getMembers(): iterable
    {
        return $this->members;
    }

    /** @param iterable<\Ibexa\Contracts\CorporateAccount\Values\Member> $members */
    public function setMembers(iterable $members): void
    {
        $this->members = $members;
    }

    /** @return iterable<\Ibexa\Contracts\User\Invitation\Invitation> */
    public function getInvitations(): iterable
    {
        return $this->invitations;
    }

    /** @param iterable<\Ibexa\Contracts\User\Invitation\Invitation> $invitations */
    public function setInvitations(iterable $invitations): void
    {
        $this->invitations = $invitations;
    }

    public function getMembersSearchForm(): FormInterface
    {
        return $this->membersSearchForm;
    }

    public function setMembersSearchForm(FormInterface $membersSearchForm): void
    {
        $this->membersSearchForm = $membersSearchForm;
    }

    public function getChangeRoleForm(): FormInterface
    {
        return $this->changeRoleForm;
    }

    public function setChangeRoleForm(FormInterface $changeRoleForm): void
    {
        $this->changeRoleForm = $changeRoleForm;
    }

    public function getInvitationForm(): FormInterface
    {
        return $this->invitationForm;
    }

    public function setInvitationForm(FormInterface $invitationForm): void
    {
        $this->invitationForm = $invitationForm;
    }

    public function getInvitationResendForm(): FormInterface
    {
        return $this->invitationResendForm;
    }

    public function setInvitationResendForm(FormInterface $invitationResendForm): void
    {
        $this->invitationResendForm = $invitationResendForm;
    }

    public function getInvitationReinviteForm(): FormInterface
    {
        return $this->invitationReinviteForm;
    }

    public function setInvitationReinviteForm(FormInterface $invitationReinviteForm): void
    {
        $this->invitationReinviteForm = $invitationReinviteForm;
    }
}
