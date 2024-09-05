<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CorporateAccount\Event\ShippingAddress;

use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Contracts\CorporateAccount\Values\ShippingAddress;
use Ibexa\Contracts\CorporateAccount\Values\ShippingAddressCreateStruct;
use Symfony\Contracts\EventDispatcher\Event;
use UnexpectedValueException;

final class BeforeCreateShippingAddressEvent extends Event
{
    private Company $company;

    private ShippingAddressCreateStruct $shippingAddressCreateStruct;

    /** @var string[]|null */
    private ?array $fieldIdentifiersToValidate;

    private ?ShippingAddress $shippingAddress = null;

    /**
     * @param string[]|null $fieldIdentifiersToValidate
     */
    public function __construct(
        Company $company,
        ShippingAddressCreateStruct $shippingAddressCreateStruct,
        ?array $fieldIdentifiersToValidate = null
    ) {
        $this->company = $company;
        $this->shippingAddressCreateStruct = $shippingAddressCreateStruct;
        $this->fieldIdentifiersToValidate = $fieldIdentifiersToValidate;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function getShippingAddressCreateStruct(): ShippingAddressCreateStruct
    {
        return $this->shippingAddressCreateStruct;
    }

    /**
     * @return string[]|null
     */
    public function getFieldIdentifiersToValidate(): ?array
    {
        return $this->fieldIdentifiersToValidate;
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
