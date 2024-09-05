<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CorporateAccount\Service;

use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Contracts\CorporateAccount\Values\ShippingAddress;
use Ibexa\Contracts\CorporateAccount\Values\ShippingAddressCreateStruct;
use Ibexa\Contracts\CorporateAccount\Values\ShippingAddressUpdateStruct;

interface ShippingAddressService
{
    public function getShippingAddress(int $id): ShippingAddress;

    public function getCompanyDefaultShippingAddress(
        Company $company
    ): ?ShippingAddress;

    /** @return \Ibexa\Contracts\CorporateAccount\Values\ShippingAddress[] */
    public function getCompanyShippingAddresses(
        Company $company,
        int $limit = 25,
        int $offset = 0
    ): array;

    /**
     * @param string[]|null $fieldIdentifiersToValidate
     */
    public function createShippingAddress(
        Company $company,
        ShippingAddressCreateStruct $shippingAddressCreateStruct,
        ?array $fieldIdentifiersToValidate = null
    ): ShippingAddress;

    /**
     * @param string[]|null $fieldIdentifiersToValidate
     */
    public function updateShippingAddress(
        ShippingAddress $shippingAddress,
        ShippingAddressUpdateStruct $shippingAddressUpdateStruct,
        ?array $fieldIdentifiersToValidate = null
    ): ShippingAddress;

    public function deleteShippingAddress(ShippingAddress $shippingAddress): void;

    public function createShippingAddressFromCompanyBillingAddress(Company $company): ShippingAddress;

    public function newShippingAddressCreateStruct(): ShippingAddressCreateStruct;

    public function newShippingAddressUpdateStruct(): ShippingAddressUpdateStruct;
}
