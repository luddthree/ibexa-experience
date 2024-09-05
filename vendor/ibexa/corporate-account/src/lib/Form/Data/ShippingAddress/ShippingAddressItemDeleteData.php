<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Form\Data\ShippingAddress;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

final class ShippingAddressItemDeleteData extends ValueObject
{
    /** @var \Ibexa\Contracts\CorporateAccount\Values\ShippingAddress[]|null */
    public ?array $addressBookItems;

    /** @param \Ibexa\Contracts\CorporateAccount\Values\ShippingAddress[] $addressBookItems */
    public function __construct(?array $addressBookItems = null)
    {
        $this->addressBookItems = $addressBookItems;
    }

    /** @return \Ibexa\Contracts\CorporateAccount\Values\ShippingAddress[]|null */
    public function getAddressBookItems(): ?array
    {
        return $this->addressBookItems;
    }

    /** @param \Ibexa\Contracts\CorporateAccount\Values\ShippingAddress[]|null $addressBookItems */
    public function setAddressBookItems(?array $addressBookItems): void
    {
        $this->addressBookItems = $addressBookItems;
    }
}
