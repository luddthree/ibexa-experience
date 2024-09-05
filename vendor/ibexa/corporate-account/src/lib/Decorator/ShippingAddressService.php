<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Decorator;

use Ibexa\Contracts\CorporateAccount\Service\ShippingAddressService as ShippingAddressServiceInterface;
use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Contracts\CorporateAccount\Values\ShippingAddress;
use Ibexa\Contracts\CorporateAccount\Values\ShippingAddressCreateStruct;
use Ibexa\Contracts\CorporateAccount\Values\ShippingAddressUpdateStruct;

abstract class ShippingAddressService implements ShippingAddressServiceInterface
{
    protected ShippingAddressServiceInterface $innerService;

    public function __construct(
        ShippingAddressServiceInterface $innerService
    ) {
        $this->innerService = $innerService;
    }

    public function getShippingAddress(int $id): ShippingAddress
    {
        return $this->innerService->getShippingAddress($id);
    }

    public function getCompanyDefaultShippingAddress(Company $company): ?ShippingAddress
    {
        return $this->innerService->getCompanyDefaultShippingAddress($company);
    }

    public function getCompanyShippingAddresses(
        Company $company,
        int $limit = 25,
        int $offset = 0
    ): array {
        return $this->innerService->getCompanyShippingAddresses($company, $limit, $offset);
    }

    public function createShippingAddress(
        Company $company,
        ShippingAddressCreateStruct $shippingAddressCreateStruct,
        ?array $fieldIdentifiersToValidate = null
    ): ShippingAddress {
        return $this->innerService->createShippingAddress($company, $shippingAddressCreateStruct, $fieldIdentifiersToValidate);
    }

    public function updateShippingAddress(
        ShippingAddress $shippingAddress,
        ShippingAddressUpdateStruct $shippingAddressUpdateStruct,
        ?array $fieldIdentifiersToValidate = null
    ): ShippingAddress {
        return $this->innerService->updateShippingAddress($shippingAddress, $shippingAddressUpdateStruct, $fieldIdentifiersToValidate);
    }

    public function deleteShippingAddress(ShippingAddress $shippingAddress): void
    {
        $this->innerService->deleteShippingAddress($shippingAddress);
    }

    public function createShippingAddressFromCompanyBillingAddress(Company $company): ShippingAddress
    {
        return $this->innerService->createShippingAddressFromCompanyBillingAddress($company);
    }

    public function newShippingAddressCreateStruct(): ShippingAddressCreateStruct
    {
        return $this->innerService->newShippingAddressCreateStruct();
    }

    public function newShippingAddressUpdateStruct(): ShippingAddressUpdateStruct
    {
        return $this->innerService->newShippingAddressUpdateStruct();
    }
}
