<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Commerce\Orders\Null;

use Ibexa\CorporateAccount\Commerce\Orders\OrdersFilter;
use Ibexa\CorporateAccount\Commerce\Orders\OrdersProviderInterface;
use Ibexa\CorporateAccount\Commerce\Orders\OrdersSum;

final class NullOrdersProvider implements OrdersProviderInterface
{
    public function getCompanyOrderList(OrdersFilter $ordersFilter, int $offset = 0, ?int $limit = 30): array
    {
        return [];
    }

    public function getCompanyOrdersCount(OrdersFilter $ordersFilter): int
    {
        return 0;
    }

    public function getOrdersSum(OrdersFilter $filter): OrdersSum
    {
        return new OrdersSum(0.0, '', 0);
    }
}
