<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\DataTransformer;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\ProductCatalog\AttributeGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @implements DataTransformerInterface<AttributeGroupInterface, string>
 */
final class AttributeGroupTransformer implements DataTransformerInterface
{
    private AttributeGroupServiceInterface $attributeGroupService;

    public function __construct(AttributeGroupServiceInterface $attributeGroupService)
    {
        $this->attributeGroupService = $attributeGroupService;
    }

    public function transform($value): ?string
    {
        if (null === $value) {
            return null;
        }

        if (!($value instanceof AttributeGroupInterface)) {
            throw new TransformationFailedException('Expected a ' . AttributeGroupInterface::class . ' object.');
        }

        return $value->getIdentifier();
    }

    public function reverseTransform($value): ?AttributeGroupInterface
    {
        if (empty($value)) {
            return null;
        }

        if (!is_string($value)) {
            throw new TransformationFailedException('Invalid data, expected a string value');
        }

        try {
            return $this->attributeGroupService->getAttributeGroup($value);
        } catch (NotFoundException | UnauthorizedException $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
