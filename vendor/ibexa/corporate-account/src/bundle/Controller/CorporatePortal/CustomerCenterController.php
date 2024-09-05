<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\Controller\CorporatePortal;

use Ibexa\Bundle\CorporateAccount\Controller\Controller;
use Ibexa\Contracts\CorporateAccount\Permission\MemberResolver;
use Ibexa\Core\MVC\Symfony\View\BaseView;
use Ibexa\CorporateAccount\Commerce\Orders\OrdersFilter;
use Ibexa\CorporateAccount\Commerce\Orders\OrdersProviderInterface;
use Ibexa\CorporateAccount\Configuration\CorporateAccount;
use Ibexa\CorporateAccount\View\CorporatePortal\CustomerCenterView;
use Symfony\Component\HttpFoundation\Request;

final class CustomerCenterController extends Controller
{
    private MemberResolver $memberResolver;

    private OrdersProviderInterface $ordersProvider;

    public function __construct(
        CorporateAccount $corporateAccount,
        MemberResolver $memberResolver,
        OrdersProviderInterface $ordersProvider
    ) {
        parent::__construct($corporateAccount);
        $this->memberResolver = $memberResolver;
        $this->ordersProvider = $ordersProvider;
    }

    public function showAction(Request $request): BaseView
    {
        $company = $this->memberResolver->getCurrentMember()->getCompany();
        $ordersFilter = new OrdersFilter($company, [OrdersFilter::STATUS_PAID, OrdersFilter::STATUS_CONFIRMED]);

        $orders = $this->ordersProvider->getCompanyOrderList(
            $ordersFilter,
            0,
            null
        );

        $ordersSum = $this
            ->ordersProvider
            ->getOrdersSum($ordersFilter);

        return new CustomerCenterView(
            '@ibexadesign/customer_portal/dashboard/dashboard.html.twig',
            $company,
            array_slice($orders, 0, 10, true),
            $ordersSum
        );
    }
}
