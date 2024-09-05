<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\DataTransformer;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ColorAttribute;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @implements DataTransformerInterface<ColorAttribute, string[]|null>
 */
final class ColorAttributeCriterionTransformer implements DataTransformerInterface
{
    private string $identifier;

    public function __construct(string $identifier)
    {
        $this->identifier = $identifier;
    }

    public function transform($value): ?array
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof ColorAttribute) {
            throw new TransformationFailedException(
                sprintf(
                    'Expected a %s object, received %s.',
                    ColorAttribute::class,
                    get_debug_type($value)
                )
            );
        }

        return $value->getValue();
    }

    public function reverseTransform($value): ?ColorAttribute
    {
        if (null === $value) {
            return null;
        }

        if (!is_array($value)) {
            throw new TransformationFailedException(
                sprintf(
                    'Invalid data, expected an array value, received %s.',
                    get_debug_type($value)
                )
            );
        }

        return new ColorAttribute(
            $this->identifier,
            $value
        );
    }
}
