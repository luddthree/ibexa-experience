<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\DataTransformer;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductAvailability;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductAvailability as ProductAvailabilityCriterion;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @implements DataTransformerInterface<ProductAvailability, bool>
 */
final class ProductAvailabilityCriterionTransformer implements DataTransformerInterface
{
    public function transform($value): ?bool
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof ProductAvailability) {
            throw new TransformationFailedException('Expected a ' . ProductAvailability::class . ' object.');
        }

        return $value->isAvailable();
    }

    public function reverseTransform($value): ?ProductAvailability
    {
        if ($value === null) {
            return null;
        }

        if (!is_bool($value)) {
            throw new TransformationFailedException('Invalid data, expected a boolean value');
        }

        return new ProductAvailabilityCriterion(
            $value
        );
    }
}
