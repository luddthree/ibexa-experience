<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Commerce\Orders;

use DateTimeInterface;
use Ibexa\Contracts\CorporateAccount\Values\Company;

final class OrdersFilter
{
    public const STATUS_PAID = 'paid';
    public const STATUS_CONFIRMED = 'confirmed';

    public Company $company;

    /** @var string[] */
    public array $orderStatusList;

    public ?string $transactionId;

    public ?DateTimeInterface $from;

    public ?DateTimeInterface $to;

    /** @param string[] $orderStatusList */
    public function __construct(
        Company $company,
        array $orderStatusList = [],
        ?string $transactionId = null,
        ?DateTimeInterface $from = null,
        ?DateTimeInterface $to = null
    ) {
        $this->company = $company;
        $this->orderStatusList = $orderStatusList;
        $this->transactionId = $transactionId;
        $this->from = $from;
        $this->to = $to;
    }
}
