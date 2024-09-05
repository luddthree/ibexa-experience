<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Commerce\Orders;

use DateTimeInterface;
use Ibexa\Contracts\CorporateAccount\Values\Member;
use Money\Money;

interface OrderInterface
{
    public function getId(): string;

    public function getMember(): Member;

    public function getDate(): DateTimeInterface;

    public function getTotal(): Money;

    public function getState(): string;

    public function getShippingMethod(): string;

    public function getInvoice(): ?InvoiceInterface;
}
