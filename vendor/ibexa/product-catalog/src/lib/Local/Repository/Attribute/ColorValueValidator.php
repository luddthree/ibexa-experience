<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Attribute;

use Ibexa\Contracts\ProductCatalog\Local\Attribute\ValueValidationError;
use Ibexa\Contracts\ProductCatalog\Local\Attribute\ValueValidatorInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;

final class ColorValueValidator implements ValueValidatorInterface
{
    private const VALUE_PATTERN = '/^#[0-9A-Fa-f]{6}$/';

    public function validateValue(AttributeDefinitionInterface $attributeDefinition, $value): iterable
    {
        $errors = [];
        if ($value === null) {
            return $errors;
        }

        if (!is_string($value)) {
            $errors[] = new ValueValidationError(null, sprintf('Expected string, got %s', is_object($value) ? get_class($value) : gettype($value)));
        } elseif (preg_match(self::VALUE_PATTERN, $value) !== 1) {
            $errors[] = new ValueValidationError(null, sprintf("Value doesn't match %s pattern", self::VALUE_PATTERN));
        }

        return $errors;
    }
}
