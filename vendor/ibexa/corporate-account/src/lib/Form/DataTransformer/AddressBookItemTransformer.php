<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Form\DataTransformer;

use Ibexa\Contracts\CorporateAccount\Service\ShippingAddressService;
use Ibexa\Contracts\CorporateAccount\Values\ShippingAddress;
use Ibexa\Contracts\Rest\Exceptions\NotFoundException;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class AddressBookItemTransformer implements DataTransformerInterface
{
    private ShippingAddressService $shippingAddressService;

    public function __construct(ShippingAddressService $contentService)
    {
        $this->shippingAddressService = $contentService;
    }

    public function transform($value): ?int
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof ShippingAddress) {
            throw new TransformationFailedException('Expected a ' . ShippingAddress::class . ' object.');
        }

        return $value->getId();
    }

    public function reverseTransform($value): ?ShippingAddress
    {
        if (empty($value)) {
            return null;
        }

        if (!ctype_digit($value)) {
            throw new TransformationFailedException('Expected a numeric string.');
        }

        try {
            return $this->shippingAddressService->getShippingAddress((int)$value);
        } catch (NotFoundException $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
