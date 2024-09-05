<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\Controller;

use DateTime;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\CorporateAccount\Service\MemberService;
use Ibexa\Contracts\CorporateAccount\Service\ShippingAddressService;
use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Contracts\User\Invitation\InvitationService;
use Ibexa\Contracts\User\Invitation\Query\InvitationFilter;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\CorporateAccount\Commerce\Orders\OrdersFilter;
use Ibexa\CorporateAccount\Commerce\Orders\OrdersProviderInterface;
use Ibexa\CorporateAccount\Commerce\Orders\OrdersSum;
use Ibexa\CorporateAccount\Configuration\CorporateAccountConfiguration;
use Ibexa\CorporateAccount\Form\MemberFormFactory;
use Ibexa\CorporateAccount\Form\ShippingAddressFormFactory;
use Ibexa\CorporateAccount\Pagerfanta\Adapter\MemberListAdapter;
use Ibexa\CorporateAccount\Pagerfanta\Adapter\ShippingAddressListAdapter;
use Ibexa\CorporateAccount\View\CompanyDetailsView;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;

class CompanyDetailsController extends Controller
{
    private ConfigResolverInterface $configResolver;

    private MemberFormFactory $formFactory;

    private MemberService $memberService;

    private ShippingAddressService $shippingAddressService;

    private InvitationService $invitationService;

    private OrdersProviderInterface $ordersProvider;

    private UserService $userService;

    private ShippingAddressFormFactory $addressFormFactory;

    public function __construct(
        CorporateAccountConfiguration $corporateAccount,
        ConfigResolverInterface $configResolver,
        MemberFormFactory $formFactory,
        OrdersProviderInterface $ordersProvider,
        MemberService $memberService,
        ShippingAddressService $shippingAddressService,
        InvitationService $invitationService,
        UserService $userService,
        ShippingAddressFormFactory $addressFormFactory
    ) {
        parent::__construct($corporateAccount);

        $this->configResolver = $configResolver;
        $this->formFactory = $formFactory;
        $this->invitationService = $invitationService;
        $this->ordersProvider = $ordersProvider;
        $this->memberService = $memberService;
        $this->shippingAddressService = $shippingAddressService;
        $this->userService = $userService;
        $this->addressFormFactory = $addressFormFactory;
    }

    public function detailsAction(Request $request, Company $company): CompanyDetailsView
    {
        $location = $company->getContent()->getVersionInfo()->getContentInfo()->getMainLocation();

        if (null === $location) {
            throw new NotFoundException('location', $company->getContent()->contentInfo->mainLocationId);
        }

        $searchForm = $this->formFactory->getMembersSearchForm();
        $changeRoleForm = $this->formFactory->getChangeRoleForm();
        $invitationForm = $this->formFactory->getInviteMembersWithSiteAccessForm($company);

        $criterions = [];
        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $criterions[] = new Criterion\FullText($searchForm->getData()['query']);
        }

        $members = new Pagerfanta(
            new MemberListAdapter(
                $this->memberService,
                $company,
                empty($criterions) ? new Criterion\MatchAll() : new Criterion\LogicalAnd($criterions)
            )
        );

        $members->setMaxPerPage($this->configResolver->getParameter('corporate_account.pagination.members_limit'));
        $members->setCurrentPage($request->query->getInt('page', 1));

        $addresses = new Pagerfanta(
            new ShippingAddressListAdapter(
                $this->shippingAddressService,
                $company
            )
        );
        $addresses->setMaxPerPage($this->configResolver->getParameter('corporate_account.pagination.addresses_limit'));
        $addresses->setCurrentPage($request->query->getInt('page', 1));

        $invitations = $this->invitationService->findInvitations(
            new InvitationFilter(
                null,
                $this->userService->loadUserGroup($company->getMembersId())
            )
        );

        $filter = new OrdersFilter($company, [OrdersFilter::STATUS_PAID, OrdersFilter::STATUS_CONFIRMED]);

        $lastOrders = $this->ordersProvider->getCompanyOrderList(
            $filter,
            0,
            10
        );

        return new CompanyDetailsView(
            '@ibexadesign/corporate_account/company/details.html.twig',
            $company,
            $members,
            $addresses,
            $invitations,
            $lastOrders,
            $this->getSalesThisYear($filter),
            $this->getSalesLastYear($filter),
            $searchForm,
            $changeRoleForm,
            $invitationForm,
            $this->formFactory->getInvitationResendForm($company),
            $this->formFactory->getInvitationReinviteForm($company),
            $this->addressFormFactory->getDeleteShippingAddressForm($addresses->getCurrentPageResults()),
            $this->addressFormFactory->getSetDefaultShippingAddressForm(
                $company,
                $this->shippingAddressService->getCompanyDefaultShippingAddress($company)
            )
        );
    }

    private function getSalesThisYear(OrdersFilter $filter): OrdersSum
    {
        $filter->from = new DateTime(date('Y') . '-01-01');

        return $this->ordersProvider->getOrdersSum(
            $filter
        );
    }

    private function getSalesLastYear(OrdersFilter $filter): OrdersSum
    {
        $filter->from = new DateTime((date('Y') - 1) . '-01-01');
        $filter->to = new DateTime((date('Y') - 1) . '-12-31');

        return $this->ordersProvider->getOrdersSum(
            $filter
        );
    }
}
