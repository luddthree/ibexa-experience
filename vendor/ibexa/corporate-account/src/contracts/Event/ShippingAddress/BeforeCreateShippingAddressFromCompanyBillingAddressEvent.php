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
use UnexpectedValueException;

final class BeforeCreateShippingAddressFromCompanyBillingAddressEvent extends Event
{
    private Company $company;

    private ?ShippingAddress $shippingAddress = null;

    public function __construct(
        Company $company
    ) {
        $this->company = $company;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function getShippingAddress(): ShippingAddress
    {
        if (!$this->hasShippingAddress()) {
            throw new UnexpectedValueException(sprintf('Return value is not set or not of type %s. Check hasShippingAddress() or set it using setShippingAddress() before you call the getter.', ShippingAddress::class));
        }

        return $this->shippingAddress;
    }

    public function setShippingAddress(?ShippingAddress $shippingAddress): void
    {
        $this->shippingAddress = $shippingAddress;
    }

    public function hasShippingAddress(): bool
    {
        return $this->shippingAddress instanceof ShippingAddress;
    }
}
