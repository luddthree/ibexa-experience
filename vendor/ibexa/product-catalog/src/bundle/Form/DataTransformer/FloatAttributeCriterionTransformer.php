<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\DataTransformer;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\FloatAttributeRange;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @implements DataTransformerInterface<
 *     \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\FloatAttributeRange,
 *     array{
 *         min: float|null,
 *         max: float|null,
 *     }|null
 * >
 */
final class FloatAttributeCriterionTransformer implements DataTransformerInterface
{
    private string $identifier;

    public function __construct(string $identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @phpstan-return array{
     *     min: float|null,
     *     max: float|null,
     * }|null
     */
    public function transform($value): ?array
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof FloatAttributeRange) {
            throw new TransformationFailedException(
                sprintf(
                    'Expected a %s object, received %s.',
                    FloatAttributeRange::class,
                    get_debug_type($value)
                )
            );
        }

        return [
            'min' => $value->getMin() ?: null,
            'max' => $value->getMax() ?: null,
        ];
    }

    public function reverseTransform($value): ?FloatAttributeRange
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

        if ($value['min'] === null && $value['max'] === null) {
            return null;
        }

        return new FloatAttributeRange(
            $this->identifier,
            $value['min'],
            $value['max']
        );
    }
}
