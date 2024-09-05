<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\DataTransformer;

use Ibexa\Contracts\ProductCatalog\ProductTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @implements DataTransformerInterface<ProductType, array>
 */
final class ProductTypeCriterionTransformer implements DataTransformerInterface
{
    private ProductTypeServiceInterface $productTypeService;

    public function __construct(
        ProductTypeServiceInterface $productTypeService
    ) {
        $this->productTypeService = $productTypeService;
    }

    /**
     * @return \Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface[]
     */
    public function transform($value): array
    {
        if (null === $value) {
            return [];
        }

        if (!$value instanceof ProductType) {
            throw new TransformationFailedException('Expected a ' . ProductType::class . ' object.');
        }

        $productTypes = [];
        foreach ($value->getTypes() as $type) {
            $productTypes[] = $this->productTypeService->getProductType($type);
        }

        return $productTypes;
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface[] $value
     */
    public function reverseTransform($value): ?ProductType
    {
        if (!is_array($value)) {
            throw new TransformationFailedException('Invalid data, expected an array value');
        }

        if (empty($value)) {
            return null;
        }

        $identifiers = [];
        foreach ($value as $type) {
            $identifiers[] = $type->getIdentifier();
        }

        return new ProductType(
            $identifiers
        );
    }
}
