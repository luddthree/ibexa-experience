<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\ParamConverter;

use Ibexa\Contracts\CorporateAccount\Service\ShippingAddressService;
use Ibexa\Contracts\CorporateAccount\Values\ShippingAddress;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class ShippingAddressParamConverter implements ParamConverterInterface
{
    public const PARAMETER_SHIPPING_ADDRESS = 'shippingAddress';
    public const PARAMETER_SHIPPING_ADDRESS_ID = 'shippingAddressId';

    private ShippingAddressService $shippingAddressService;

    public function __construct(
        ShippingAddressService $shippingAddressService
    ) {
        $this->shippingAddressService = $shippingAddressService;
    }

    public function apply(
        Request $request,
        ParamConverter $configuration
    ): bool {
        $shippingAddressId = $request->get(self::PARAMETER_SHIPPING_ADDRESS_ID);

        if (null === $shippingAddressId) {
            return false;
        }

        $shippingAddress = $this->shippingAddressService->getShippingAddress((int)$shippingAddressId);

        $request->attributes->set($configuration->getName(), $shippingAddress);

        return true;
    }

    public function supports(ParamConverter $configuration): bool
    {
        return ShippingAddress::class === $configuration->getClass()
            && self::PARAMETER_SHIPPING_ADDRESS === $configuration->getName();
    }
}
