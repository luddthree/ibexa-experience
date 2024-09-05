<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CorporateAccount\Event\ShippingAddress;

use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Contracts\CorporateAccount\Values\ShippingAddress;
use Symfony\Contracts\EventDispatcher\Event;

final class CreateShippingAddressFromCompanyBillingAddressEvent extends Event
{
    private ShippingAddress $shippingAddress;

    private Company $company;

    public function __construct(
        ShippingAddress $shippingAddress,
        Company $company
    ) {
        $this->shippingAddress = $shippingAddress;
        $this->company = $company;
    }

    public function getShippingAddress(): ShippingAddress
    {
        return $this->shippingAddress;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }
}
