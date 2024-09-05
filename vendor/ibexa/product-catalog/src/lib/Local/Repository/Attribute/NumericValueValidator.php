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

final class NumericValueValidator implements ValueValidatorInterface
{
    public function validateValue(AttributeDefinitionInterface $attributeDefinition, $value): iterable
    {
        if ($value === null) {
            return [];
        }

        $errors = [];
        $options = $attributeDefinition->getOptions();

        $min = $options->get('min');
        if ($min !== null && $value < $min) {
            $errors[] = new ValueValidationError(null, 'Value should be greater or equal than %min%', [
                '%min%' => $min,
            ]);
        }

        $max = $options->get('max');
        if ($max !== null && $value > $max) {
            $errors[] = new ValueValidationError(null, 'Value should be lower or equal than %max%', [
                '%max%' => $max,
            ]);
        }

        return $errors;
    }
}
