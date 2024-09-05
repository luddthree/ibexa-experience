<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CorporateAccount\Event\ShippingAddress;

use Ibexa\Contracts\CorporateAccount\Values\ShippingAddress;
use Ibexa\Contracts\CorporateAccount\Values\ShippingAddressUpdateStruct;
use Symfony\Contracts\EventDispatcher\Event;
use UnexpectedValueException;

final class BeforeUpdateShippingAddressEvent extends Event
{
    private ShippingAddress $shippingAddress;

    private ShippingAddressUpdateStruct $shippingAddressUpdateStruct;

    /**
     * @var string[]|null
     */
    private ?array $fieldIdentifiersToValidate;

    private ?ShippingAddress $updatedShippingAddress = null;

    /**
     * @param string[]|null $fieldIdentifiersToValidate
     */
    public function __construct(
        ShippingAddress $shippingAddress,
        ShippingAddressUpdateStruct $shippingAddressUpdateStruct,
        ?array $fieldIdentifiersToValidate = null
    ) {
        $this->shippingAddress = $shippingAddress;
        $this->shippingAddressUpdateStruct = $shippingAddressUpdateStruct;
        $this->fieldIdentifiersToValidate = $fieldIdentifiersToValidate;
    }

    public function getShippingAddress(): ShippingAddress
    {
        return $this->shippingAddress;
    }

    public function getShippingAddressUpdateStruct(): ShippingAddressUpdateStruct
    {
        return $this->shippingAddressUpdateStruct;
    }

    /**
     * @return string[]|null
     */
    public function getFieldIdentifiersToValidate(): ?array
    {
        return $this->fieldIdentifiersToValidate;
    }

    public function getUpdatedShippingAddress(): ShippingAddress
    {
        if (!$this->hasUpdatedShippingAddress()) {
            throw new UnexpectedValueException(sprintf('Return value is not set or not of type %s. Check hasUpdatedShippingAddress() or set it using setUpdatedShippingAddress() before you call the getter.', ShippingAddress::class));
        }

        return $this->updatedShippingAddress;
    }

    public function setUpdatedShippingAddress(?ShippingAddress $updatedShippingAddress): void
    {
        $this->updatedShippingAddress = $updatedShippingAddress;
    }

    public function hasUpdatedShippingAddress(): bool
    {
        return $this->updatedShippingAddress instanceof ShippingAddress;
    }
}
