<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\Controller\CorporatePortal;

use Ibexa\Bundle\CorporateAccount\Controller\Controller;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\CorporateAccount\Permission\MemberResolver;
use Ibexa\Core\MVC\Symfony\View\BaseView;
use Ibexa\CorporateAccount\Commerce\Orders\OrdersFilter;
use Ibexa\CorporateAccount\Commerce\Orders\OrdersProviderInterface;
use Ibexa\CorporateAccount\Configuration\CorporateAccount;
use Ibexa\CorporateAccount\Pagerfanta\Adapter\OrderListAdapter;
use Ibexa\CorporateAccount\View\CorporatePortal\OrdersView;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;

final class OrdersController extends Controller
{
    private OrdersProviderInterface $ordersProvider;

    private ConfigResolverInterface $configResolver;

    private MemberResolver $membersResolver;

    public function __construct(
        CorporateAccount $corporateAccount,
        MemberResolver $membersResolver,
        OrdersProviderInterface $ordersProvider,
        ConfigResolverInterface $configResolver
    ) {
        parent::__construct($corporateAccount);
        $this->ordersProvider = $ordersProvider;
        $this->configResolver = $configResolver;
        $this->membersResolver = $membersResolver;
    }

    public function showPastOrdersAction(Request $request): BaseView
    {
        $company = $this->membersResolver->getCurrentMember()->getCompany();

        $pagerfanta = new Pagerfanta(
            new OrderListAdapter(
                $this->ordersProvider,
                new OrdersFilter($company, [OrdersFilter::STATUS_PAID])
            )
        );

        $pagerfanta->setMaxPerPage($this->configResolver->getParameter('corporate_account.pagination.orders_limit'));
        $pagerfanta->setCurrentPage($request->query->getInt('page', 1));

        return new OrdersView(
            '@ibexadesign/customer_portal/orders/past_orders.html.twig',
            $pagerfanta,
            $company
        );
    }

    public function showPendingOrdersAction(Request $request): BaseView
    {
        $company = $this->membersResolver->getCurrentMember()->getCompany();

        $pagerfanta = new Pagerfanta(
            new OrderListAdapter(
                $this->ordersProvider,
                new OrdersFilter($company, [OrdersFilter::STATUS_CONFIRMED])
            )
        );

        $pagerfanta->setMaxPerPage($this->configResolver->getParameter('corporate_account.pagination.orders_limit'));
        $pagerfanta->setCurrentPage($request->query->getInt('page', 1));

        return new OrdersView(
            '@ibexadesign/customer_portal/orders/pending_orders.html.twig',
            $pagerfanta,
            $company
        );
    }
}
