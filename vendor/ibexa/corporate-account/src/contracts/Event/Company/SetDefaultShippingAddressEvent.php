<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CorporateAccount\Event\Company;

use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Contracts\CorporateAccount\Values\ShippingAddress;
use Symfony\Contracts\EventDispatcher\Event;

final class SetDefaultShippingAddressEvent extends Event
{
    private Company $company;

    private ShippingAddress $shippingAddress;

    public function __construct(
        Company $company,
        ShippingAddress $shippingAddress
    ) {
        $this->company = $company;
        $this->shippingAddress = $shippingAddress;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function getShippingAddress(): ShippingAddress
    {
        return $this->shippingAddress;
    }
}
