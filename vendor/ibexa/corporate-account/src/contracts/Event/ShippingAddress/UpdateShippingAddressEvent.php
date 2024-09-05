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

final class UpdateShippingAddressEvent extends Event
{
    private ShippingAddress $updatedShippingAddress;

    private ShippingAddress $shippingAddress;

    private ShippingAddressUpdateStruct $shippingAddressUpdateStruct;

    /**
     * @var string[]|null
     */
    private ?array $fieldIdentifiersToValidate;

    /**
     * @param string[]|null $fieldIdentifiersToValidate
     */
    public function __construct(
        ShippingAddress $updatedShippingAddress,
        ShippingAddress $shippingAddress,
        ShippingAddressUpdateStruct $shippingAddressUpdateStruct,
        ?array $fieldIdentifiersToValidate = null
    ) {
        $this->updatedShippingAddress = $updatedShippingAddress;
        $this->shippingAddress = $shippingAddress;
        $this->shippingAddressUpdateStruct = $shippingAddressUpdateStruct;
        $this->fieldIdentifiersToValidate = $fieldIdentifiersToValidate;
    }

    public function getUpdatedShippingAddress(): ShippingAddress
    {
        return $this->updatedShippingAddress;
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
}
