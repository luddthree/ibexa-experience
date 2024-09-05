<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @implements \Symfony\Component\Form\DataTransformerInterface<?string, array{codes?: array<string>}>
 */
final class ProductListAttributeTransformer implements DataTransformerInterface
{
    public function transform($value): ?array
    {
        if (null === $value) {
            return null;
        }

        if (!is_string($value)) {
            throw new TransformationFailedException('Invalid data, string value expected');
        }

        return [
            'codes' => explode(',', $value),
        ];
    }

    public function reverseTransform($value): ?string
    {
        if (null === $value) {
            return null;
        }

        if (!is_array($value)) {
            throw new TransformationFailedException('Invalid data, array value expected');
        }

        if (!isset($value['codes'])) {
            throw new TransformationFailedException('Invalid data. Missing "codes" key');
        }

        $products = $value['codes'];

        return implode(',', $products);
    }
}
