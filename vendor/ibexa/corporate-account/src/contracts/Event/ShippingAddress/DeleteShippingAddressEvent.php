<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CorporateAccount\Event\ShippingAddress;

use Ibexa\Contracts\CorporateAccount\Values\ShippingAddress;
use Symfony\Contracts\EventDispatcher\Event;

final class DeleteShippingAddressEvent extends Event
{
    private ShippingAddress $shippingAddress;

    public function __construct(ShippingAddress $shippingAddress)
    {
        $this->shippingAddress = $shippingAddress;
    }

    public function getShippingAddress(): ShippingAddress
    {
        return $this->shippingAddress;
    }
}
