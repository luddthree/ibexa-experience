<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\DataTransformer;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @implements DataTransformerInterface<CustomerGroupInterface, string|int>
 */
final class CustomerGroupTransformer implements DataTransformerInterface
{
    private CustomerGroupServiceInterface $customerGroupService;

    public function __construct(CustomerGroupServiceInterface $customerGroupService)
    {
        $this->customerGroupService = $customerGroupService;
    }

    public function transform($value): ?int
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof CustomerGroupInterface) {
            throw new TransformationFailedException('Expected a ' . CustomerGroupInterface::class . ' object.');
        }

        return $value->getId();
    }

    public function reverseTransform($value): ?CustomerGroupInterface
    {
        if (empty($value)) {
            return null;
        }

        if (!is_numeric($value)) {
            throw new TransformationFailedException("Invalid data, expected a integer'ish value");
        }

        try {
            return $this->customerGroupService->getCustomerGroup((int) $value);
        } catch (NotFoundException | UnauthorizedException $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
