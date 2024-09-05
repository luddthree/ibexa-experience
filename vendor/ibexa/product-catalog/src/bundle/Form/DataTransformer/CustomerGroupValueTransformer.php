<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\DataTransformer;

use Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\ProductCatalog\FieldType\CustomerGroup\Value;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class CustomerGroupValueTransformer implements DataTransformerInterface
{
    private CustomerGroupServiceInterface $customerGroupService;

    public function __construct(CustomerGroupServiceInterface $customerGroupService)
    {
        $this->customerGroupService = $customerGroupService;
    }

    public function transform($value): ?CustomerGroupInterface
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof Value) {
            throw new TransformationFailedException('Expected a ' . Value::class . ' instance');
        }

        $id = $value->getId();

        if ($id === null) {
            return null;
        }

        try {
            $customerGroup = $this->customerGroupService->getCustomerGroup($id);
        } catch (NotFoundException $e) {
            throw new TransformationFailedException(
                sprintf('CustomerGroup with ID %s not found', $id),
                $e->getCode(),
                $e,
            );
        }

        return $customerGroup;
    }

    public function reverseTransform($value): ?Value
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof CustomerGroupInterface) {
            throw new TransformationFailedException('Expected a ' . CustomerGroupInterface::class . ' instance');
        }

        return new Value($value->getId());
    }
}
