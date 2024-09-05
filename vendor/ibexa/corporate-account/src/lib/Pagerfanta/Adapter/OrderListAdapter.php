<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Pagerfanta\Adapter;

use Ibexa\CorporateAccount\Commerce\Orders\OrdersFilter;
use Ibexa\CorporateAccount\Commerce\Orders\OrdersProviderInterface;
use Pagerfanta\Adapter\AdapterInterface;

/**
 * @implements \Pagerfanta\Adapter\AdapterInterface<\Ibexa\Bundle\Commerce\Checkout\Entity\Basket>
 */
final class OrderListAdapter implements AdapterInterface
{
    private OrdersProviderInterface $orderProvider;

    private OrdersFilter $filter;

    public function __construct(OrdersProviderInterface $ordersProvider, OrdersFilter $filter)
    {
        $this->orderProvider = $ordersProvider;
        $this->filter = $filter;
    }

    public function getNbResults(): int
    {
        return $this->orderProvider->getCompanyOrdersCount($this->filter);
    }

    public function getSlice($offset, $length)
    {
        return $this->orderProvider->getCompanyOrderList($this->filter, $offset, $length);
    }
}
