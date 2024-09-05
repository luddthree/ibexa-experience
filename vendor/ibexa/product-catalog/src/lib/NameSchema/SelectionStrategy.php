<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\NameSchema;

use Ibexa\Contracts\ProductCatalog\NameSchema\NameSchemaStrategyInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;

final class SelectionStrategy implements NameSchemaStrategyInterface
{
    /**
     * @param mixed $value
     */
    public function resolve(AttributeDefinitionInterface $attributeDefinition, $value, string $languageCode): string
    {
        /** @var array<array<string, string>> $choice */
        foreach ($attributeDefinition->getOptions()->get('choices') as $choice) {
            if ($choice['value'] === $value && array_key_exists($languageCode, $choice['label'])) {
                return $choice['label'][$languageCode];
            }
        }

        return (string)$value;
    }

    /**
     * @param mixed $value
     */
    public function supports(AttributeDefinitionInterface $attributeDefinition, $value): bool
    {
        return $attributeDefinition->getType()->getIdentifier() === 'selection';
    }
}
