<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\DataTransformer;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\ProductCatalog\ProductTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @implements DataTransformerInterface<ProductTypeInterface, string>
 */
final class ProductTypeTransformer implements DataTransformerInterface
{
    private ProductTypeServiceInterface $productTypeService;

    public function __construct(ProductTypeServiceInterface $productTypeService)
    {
        $this->productTypeService = $productTypeService;
    }

    public function transform($value): ?string
    {
        if (null === $value) {
            return null;
        }

        if (!($value instanceof ProductTypeInterface)) {
            throw new TransformationFailedException('Expected a ' . ProductTypeInterface::class . ' object.');
        }

        return $value->getIdentifier();
    }

    public function reverseTransform($value): ?ProductTypeInterface
    {
        if (empty($value)) {
            return null;
        }

        if (!is_string($value)) {
            throw new TransformationFailedException('Invalid data, expected a string value');
        }

        try {
            return $this->productTypeService->getProductType($value);
        } catch (NotFoundException | UnauthorizedException $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
