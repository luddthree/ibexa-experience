<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @implements \Symfony\Component\Form\DataTransformerInterface<int, int>
 */
final class NegativeNumberTransformer implements DataTransformerInterface
{
    public function transform($value): ?int
    {
        if (null === $value) {
            return null;
        }

        if (!is_int($value)) {
            throw new TransformationFailedException(
                sprintf(
                    'Invalid data. Value should be type of: int. %s given.',
                    is_object($value) ? get_class($value) : gettype($value)
                )
            );
        }

        if ($value > 0) {
            return $value;
        }

        return abs($value);
    }

    public function reverseTransform($value): ?int
    {
        if (null === $value) {
            return null;
        }

        if (!is_int($value)) {
            throw new TransformationFailedException(
                sprintf(
                    'Invalid data. Value should be type of: int. %s given.',
                    is_object($value) ? get_class($value) : gettype($value)
                )
            );
        }

        if ($value < 0) {
            return $value;
        }

        return -abs($value);
    }
}

class_alias(NegativeNumberTransformer::class, 'Ibexa\Platform\Personalization\Form\DataTransformer\NegativeNumberTransformer');
