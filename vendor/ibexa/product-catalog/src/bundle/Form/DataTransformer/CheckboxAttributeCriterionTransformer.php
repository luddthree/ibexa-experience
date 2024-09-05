<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\DataTransformer;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\CheckboxAttribute;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @implements DataTransformerInterface<CheckboxAttribute, bool|null>
 */
final class CheckboxAttributeCriterionTransformer implements DataTransformerInterface
{
    private string $identifier;

    public function __construct(string $identifier)
    {
        $this->identifier = $identifier;
    }

    public function transform($value): ?bool
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof CheckboxAttribute) {
            throw new TransformationFailedException(
                sprintf(
                    'Expected a %s object, received %s.',
                    CheckboxAttribute::class,
                    get_debug_type($value)
                )
            );
        }

        return $value->getValue();
    }

    public function reverseTransform($value): ?CheckboxAttribute
    {
        if ($value === null) {
            return null;
        }

        if (!is_bool($value)) {
            throw new TransformationFailedException(
                sprintf(
                    'Invalid data, expected a boolean value, received %s.',
                    get_debug_type($value)
                )
            );
        }

        return new CheckboxAttribute(
            $this->identifier,
            $value
        );
    }
}
