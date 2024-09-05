<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\View;

use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Core\MVC\Symfony\View\BaseView;
use Ibexa\CorporateAccount\Commerce\Orders\OrdersSum;
use Symfony\Component\Form\FormInterface;

class CompanyDetailsView extends BaseView
{
    private Company $company;

    /** @var iterable<\Ibexa\Contracts\CorporateAccount\Values\Member> */
    private iterable $members;

    /** @var iterable<\Ibexa\Contracts\Core\Repository\Values\Content\Content> */
    private iterable $addresses;

    private FormInterface $membersSearchForm;

    private FormInterface $changeRoleForm;

    private FormInterface $invitationForm;

    /** @var iterable<\Ibexa\Contracts\User\Invitation\Invitation> */
    private iterable $invitations;

    /** @var iterable<\Ibexa\CorporateAccount\Commerce\Orders\OrderInterface> */
    private iterable $lastOrders;

    private OrdersSum $salesThisYear;

    private OrdersSum $salesLastYear;

    private FormInterface $invitationResendForm;

    private FormInterface $invitationReinviteForm;

    private FormInterface $deleteShippingAddressesForm;

    private FormInterface $defaultShippingAddressesForm;

    /**
     * @param iterable<\Ibexa\Contracts\CorporateAccount\Values\Member> $members
     * @param iterable<\Ibexa\Contracts\Core\Repository\Values\Content\Content> $addresses
     * @param iterable<\Ibexa\Contracts\User\Invitation\Invitation> $invitations
     * @param iterable<\Ibexa\CorporateAccount\Commerce\Orders\OrderInterface> $lastOrders
     */
    public function __construct(
        string $templateIdentifier,
        Company $company,
        iterable $members,
        iterable $addresses,
        iterable $invitations,
        iterable $lastOrders,
        OrdersSum $salesThisYear,
        OrdersSum $salesLastYear,
        FormInterface $membersSearchForm,
        FormInterface $changeRoleForm,
        FormInterface $invitationForm,
        FormInterface $invitationResendForm,
        FormInterface $invitationReinviteForm,
        FormInterface $deleteShippingAddressesForm,
        FormInterface $defaultShippingAddressesForm
    ) {
        parent::__construct($templateIdentifier);

        $this->company = $company;
        $this->members = $members;
        $this->addresses = $addresses;
        $this->invitations = $invitations;
        $this->membersSearchForm = $membersSearchForm;
        $this->changeRoleForm = $changeRoleForm;
        $this->lastOrders = $lastOrders;
        $this->salesThisYear = $salesThisYear;
        $this->salesLastYear = $salesLastYear;
        $this->invitationForm = $invitationForm;
        $this->invitationResendForm = $invitationResendForm;
        $this->invitationReinviteForm = $invitationReinviteForm;
        $this->deleteShippingAddressesForm = $deleteShippingAddressesForm;
        $this->defaultShippingAddressesForm = $defaultShippingAddressesForm;
    }

    /**
     * @return array{
     *     company: \Ibexa\Contracts\CorporateAccount\Values\Company,
     *     addresses: iterable<\Ibexa\Contracts\Core\Repository\Values\Content\Content>,
     *     invitations: iterable<\Ibexa\Contracts\User\Invitation\Invitation>,
     *     members: iterable<\Ibexa\Contracts\CorporateAccount\Values\Member>,
     *     members_search_form: \Symfony\Component\Form\FormView,
     *     last_orders: iterable<\Ibexa\CorporateAccount\Commerce\Orders\OrderInterface>,
     *     sales_last_year: \Ibexa\CorporateAccount\Commerce\Orders\OrdersSum,
     *     sales_this_year: \Ibexa\CorporateAccount\Commerce\Orders\OrdersSum,
     *     invitation_form: \Symfony\Component\Form\FormView,
     *     invitation_resend_form: \Symfony\Component\Form\FormView,
     *     invitation_reinvite_form: \Symfony\Component\Form\FormView
     * }
     */
    protected function getInternalParameters()
    {
        return [
            'company' => $this->company,
            'addresses' => $this->addresses,
            'members' => $this->members,
            'invitations' => $this->invitations,
            'members_search_form' => $this->membersSearchForm->createView(),
            'change_role_form' => $this->changeRoleForm->createView(),
            'invitation_form' => $this->invitationForm->createView(),
            'last_orders' => $this->lastOrders,
            'sales_last_year' => $this->salesLastYear,
            'sales_this_year' => $this->salesThisYear,
            'invitation_resend_form' => $this->invitationResendForm->createView(),
            'invitation_reinvite_form' => $this->invitationReinviteForm->createView(),
            'delete_address_form' => $this->deleteShippingAddressesForm->createView(),
            'default_shipping_form' => $this->defaultShippingAddressesForm->createView(),
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

    /** @return iterable<\Ibexa\Contracts\Core\Repository\Values\Content\Content> */
    public function getAddresses(): iterable
    {
        return $this->addresses;
    }

    /** @param iterable<\Ibexa\Contracts\Core\Repository\Values\Content\Content> $addresses */
    public function setAddresses(iterable $addresses): void
    {
        $this->addresses = $addresses;
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

    /**
     * @return iterable<\Ibexa\Contracts\User\Invitation\Invitation>
     */
    public function getInvitations(): iterable
    {
        return $this->invitations;
    }

    /**
     * @param iterable<\Ibexa\Contracts\User\Invitation\Invitation> $invitations
     */
    public function setInvitations(iterable $invitations): void
    {
        $this->invitations = $invitations;
    }

    /**
     * @return iterable<\Ibexa\CorporateAccount\Commerce\Orders\OrderInterface>
     */
    public function getLastOrders(): iterable
    {
        return $this->lastOrders;
    }

    /**
     * @param iterable<\Ibexa\CorporateAccount\Commerce\Orders\OrderInterface> $lastOrders
     */
    public function setLastOrders(iterable $lastOrders): void
    {
        $this->lastOrders = $lastOrders;
    }

    public function getSalesThisYear(): OrdersSum
    {
        return $this->salesThisYear;
    }

    public function setSalesThisYear(OrdersSum $salesThisYear): void
    {
        $this->salesThisYear = $salesThisYear;
    }

    public function getSalesLastYear(): OrdersSum
    {
        return $this->salesLastYear;
    }

    public function setSalesLastYear(OrdersSum $salesLastYear): void
    {
        $this->salesLastYear = $salesLastYear;
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

    public function getDeleteShippingAddressesForm(): FormInterface
    {
        return $this->deleteShippingAddressesForm;
    }

    public function setDeleteShippingAddressesForm(FormInterface $deleteShippingAddressesForm): void
    {
        $this->deleteShippingAddressesForm = $deleteShippingAddressesForm;
    }
}
