<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\NameSchema;

use Ibexa\Contracts\ProductCatalog\NameSchema\NameSchemaStrategyInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;

final class NameSchemaStrategy implements NameSchemaStrategyInterface
{
    /** @var iterable<\Ibexa\Contracts\ProductCatalog\NameSchema\NameSchemaStrategyInterface> */
    private iterable $strategies;

    /**
     * @param iterable<\Ibexa\Contracts\ProductCatalog\NameSchema\NameSchemaStrategyInterface> $strategies
     */
    public function __construct(iterable $strategies)
    {
        $this->strategies = $strategies;
    }

    /**
     * @param mixed $value
     */
    public function resolve(AttributeDefinitionInterface $attributeDefinition, $value, string $languageCode): string
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($attributeDefinition, $value)) {
                return $strategy->resolve($attributeDefinition, $value, $languageCode);
            }
        }

        return '';
    }

    public function supports(AttributeDefinitionInterface $attributeDefinition, $value): bool
    {
        return false;
    }
}
