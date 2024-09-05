<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Pagerfanta\Adapter;

use Ibexa\Contracts\CorporateAccount\Service\ShippingAddressService;
use Ibexa\Contracts\CorporateAccount\Values\Company;
use Pagerfanta\Adapter\AdapterInterface;

/**
 * @implements \Pagerfanta\Adapter\AdapterInterface<\Ibexa\Contracts\Core\Repository\Values\Content\Content>
 */
class ShippingAddressListAdapter implements AdapterInterface
{
    private Company $company;

    private ShippingAddressService $shippingAddressService;

    public function __construct(
        ShippingAddressService $shippingAddressService,
        Company $company
    ) {
        $this->company = $company;
        $this->shippingAddressService = $shippingAddressService;
    }

    public function getNbResults(): int
    {
        return count($this->shippingAddressService->getCompanyShippingAddresses(
            $this->company,
            0
        ));
    }

    /** @return array<\Ibexa\Contracts\CorporateAccount\Values\ShippingAddress> */
    public function getSlice($offset, $length): array
    {
        return $this->shippingAddressService->getCompanyShippingAddresses(
            $this->company,
            $length,
            $offset
        );
    }
}
