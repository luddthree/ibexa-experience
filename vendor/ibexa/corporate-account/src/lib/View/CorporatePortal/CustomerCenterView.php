<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\View\CorporatePortal;

use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Core\MVC\Symfony\View\BaseView;
use Ibexa\CorporateAccount\Commerce\Orders\OrdersSum;

class CustomerCenterView extends BaseView
{
    private Company $company;

    /** @var iterable<\Ibexa\CorporateAccount\Commerce\Orders\OrderInterface> */
    private iterable $latestOrders;

    private OrdersSum $ordersSum;

    /**
     * @param iterable<\Ibexa\CorporateAccount\Commerce\Orders\OrderInterface> $latestOrders
     */
    public function __construct(
        string $templateIdentifier,
        Company $company,
        iterable $latestOrders,
        OrdersSum $ordersSum
    ) {
        parent::__construct($templateIdentifier);

        $this->company = $company;
        $this->latestOrders = $latestOrders;
        $this->ordersSum = $ordersSum;
    }

    /**
     * @return array{
     *     company: \Ibexa\Contracts\CorporateAccount\Values\Company,
     *     latest_orders: iterable<\Ibexa\CorporateAccount\Commerce\Orders\OrderInterface>,
     *     orders_sum: \Ibexa\CorporateAccount\Commerce\Orders\OrdersSum
     * }
     */
    protected function getInternalParameters()
    {
        return [
            'company' => $this->company,
            'latest_orders' => $this->latestOrders,
            'orders_sum' => $this->ordersSum,
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

    /**
     * @return iterable<\Ibexa\CorporateAccount\Commerce\Orders\OrderInterface>
     */
    public function getLatestOrders(): iterable
    {
        return $this->latestOrders;
    }

    /**
     * @param iterable<\Ibexa\CorporateAccount\Commerce\Orders\OrderInterface> $latestOrders
     */
    public function setLatestOrders(iterable $latestOrders): void
    {
        $this->latestOrders = $latestOrders;
    }

    public function getOrdersSum(): OrdersSum
    {
        return $this->ordersSum;
    }

    public function setOrdersSum(OrdersSum $ordersSum): void
    {
        $this->ordersSum = $ordersSum;
    }
}
