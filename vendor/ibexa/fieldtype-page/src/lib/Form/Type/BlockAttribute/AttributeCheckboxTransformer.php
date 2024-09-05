<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FieldTypePage\Form\Type\BlockAttribute;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * This transformer exists to facilitate proper handling of values persisted in database.
 *
 * @implements \Symfony\Component\Form\DataTransformerInterface<string|bool, string|bool>
 */
final class AttributeCheckboxTransformer implements DataTransformerInterface
{
    public function transform($value): ?bool
    {
        if ($value === null || is_bool($value)) {
            return $value;
        }

        if ($value === '1') {
            return true;
        }

        if ($value === '0') {
            return false;
        }

        throw new TransformationFailedException(sprintf(
            'Incorrect data in form model (%s)',
            get_debug_type($value),
        ));
    }

    public function reverseTransform($value): ?string
    {
        if ($value === null) {
            return null;
        }

        return $value ? '1' : '0';
    }
}
