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

/**
 * @phpstan-type TChoice array{value: string, label: array<string, string>}
 */
final class SelectionValueValidator implements ValueValidatorInterface
{
    public function validateValue(AttributeDefinitionInterface $attributeDefinition, $value): iterable
    {
        if ($value === null) {
            return [];
        }

        if (!$this->isExistingValue($this->getChoices($attributeDefinition), $value)) {
            return [
                new ValueValidationError(null, 'Undefined selection value: "%value%"', ['%value%' => $value]),
            ];
        }

        return [];
    }

    /**
     * @param TChoice[] $choices
     * @param mixed $value
     */
    private function isExistingValue(array $choices, $value): bool
    {
        foreach ($choices as $choice) {
            if ($value === $choice['value']) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return TChoice[]
     */
    private function getChoices(AttributeDefinitionInterface $attributeDefinition): array
    {
        return $attributeDefinition->getOptions()->get('choices', []);
    }
}
