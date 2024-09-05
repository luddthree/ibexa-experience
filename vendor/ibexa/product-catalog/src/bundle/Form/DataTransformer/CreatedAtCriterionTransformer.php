<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\DataTransformer;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\CreatedAtRange;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @implements DataTransformerInterface<
 *     \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\CreatedAtRange,
 *     array{
 *         min: DateTimeInterface|null,
 *         max: DateTimeInterface|null,
 *     }
 * >
 */
final class CreatedAtCriterionTransformer implements DataTransformerInterface
{
    public function transform($value): ?array
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof CreatedAtRange) {
            throw new TransformationFailedException(
                sprintf(
                    'Expected a %s object, received %s.',
                    CreatedAtRange::class,
                    get_debug_type($value)
                )
            );
        }

        return [
            'min' => $value->getMin() ?: null,
            'max' => $value->getMax() ?: null,
        ];
    }

    public function reverseTransform($value): ?CreatedAtRange
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

        $min = $value['min'];
        $max = $value['max'];

        if ($min === null && $max === null) {
            return null;
        }

        if ($max !== null) {
            if ($max instanceof DateTimeImmutable) {
                $max = DateTime::createFromImmutable($max);
            }

            $max->setTime(23, 59, 59);
        }

        return new CreatedAtRange($min, $max);
    }
}
