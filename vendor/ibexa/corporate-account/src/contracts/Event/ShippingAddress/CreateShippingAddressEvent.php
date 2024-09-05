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

final class CreateShippingAddressEvent extends Event
{
    private ShippingAddress $shippingAddress;

    private Company $company;

    private ShippingAddressCreateStruct $shippingAddressCreateStruct;

    /** @var string[]|null */
    private ?array $fieldIdentifiersToValidate;

    /**
     * @param string[]|null $fieldIdentifiersToValidate
     */
    public function __construct(
        ShippingAddress $shippingAddress,
        Company $company,
        ShippingAddressCreateStruct $shippingAddressCreateStruct,
        ?array $fieldIdentifiersToValidate = null
    ) {
        $this->shippingAddress = $shippingAddress;
        $this->company = $company;
        $this->shippingAddressCreateStruct = $shippingAddressCreateStruct;
        $this->fieldIdentifiersToValidate = $fieldIdentifiersToValidate;
    }

    public function getShippingAddress(): ShippingAddress
    {
        return $this->shippingAddress;
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
}
