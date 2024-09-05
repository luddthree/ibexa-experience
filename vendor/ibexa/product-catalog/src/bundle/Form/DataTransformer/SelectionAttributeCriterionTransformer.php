<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\DataTransformer;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\SelectionAttribute;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @implements DataTransformerInterface<SelectionAttribute, string[]>
 */
final class SelectionAttributeCriterionTransformer implements DataTransformerInterface
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

        if (!$value instanceof SelectionAttribute) {
            throw new TransformationFailedException(
                sprintf(
                    'Expected a %s object, received %s.',
                    SelectionAttribute::class,
                    get_debug_type($value)
                )
            );
        }

        return $value->getValue();
    }

    public function reverseTransform($value): ?SelectionAttribute
    {
        if (empty($value)) {
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

        return new SelectionAttribute(
            $this->identifier,
            $value
        );
    }
}
