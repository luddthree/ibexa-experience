<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Commerce\Orders;

interface OrdersProviderInterface
{
    /**
     * @return \Ibexa\CorporateAccount\Commerce\Orders\OrderInterface[]
     */
    public function getCompanyOrderList(OrdersFilter $ordersFilter, int $offset = 0, ?int $limit = 30): array;

    public function getCompanyOrdersCount(OrdersFilter $ordersFilter): int;

    public function getOrdersSum(OrdersFilter $filter): OrdersSum;
}
