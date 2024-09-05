<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\DataTransformer;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @implements DataTransformerInterface<AttributeDefinitionInterface, string>
 */
final class AttributeDefinitionTransformer implements DataTransformerInterface
{
    private AttributeDefinitionServiceInterface $attributeDefinitionService;

    public function __construct(AttributeDefinitionServiceInterface $attributeDefinitionService)
    {
        $this->attributeDefinitionService = $attributeDefinitionService;
    }

    public function transform($value): ?string
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof AttributeDefinitionInterface) {
            throw new TransformationFailedException('Expected a ' . AttributeDefinitionInterface::class . ' object.');
        }

        return $value->getIdentifier();
    }

    public function reverseTransform($value): ?AttributeDefinitionInterface
    {
        if (empty($value)) {
            return null;
        }

        if (!is_string($value)) {
            throw new TransformationFailedException('Invalid data, expected a string value');
        }

        try {
            return $this->attributeDefinitionService->getAttributeDefinition($value);
        } catch (NotFoundException | UnauthorizedException $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
