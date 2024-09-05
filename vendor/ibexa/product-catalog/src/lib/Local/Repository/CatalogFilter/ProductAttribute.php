<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\CatalogFilter;

use Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterDefinitionInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;

final class ProductAttribute implements FilterDefinitionInterface
{
    private AttributeDefinitionInterface $attributeDefinition;

    private string $identifier;

    /** @phpstan-var class-string<\Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\AbstractAttribute<mixed>> */
    private string $criterionClass;

    /**
     * @phpstan-param class-string<\Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\AbstractAttribute<mixed>> $criterionClass
     */
    public function __construct(
        AttributeDefinitionInterface $attributeDefinition,
        string $identifier,
        string $criterionClass
    ) {
        $this->attributeDefinition = $attributeDefinition;
        $this->identifier = $identifier;
        $this->criterionClass = $criterionClass;
    }

    public function getAttributeDefinition(): AttributeDefinitionInterface
    {
        return $this->attributeDefinition;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getName(): string
    {
        return $this->attributeDefinition->getName();
    }

    public function supports(CriterionInterface $criterion): bool
    {
        if ($criterion instanceof $this->criterionClass) {
            return $criterion->getIdentifier() === $this->attributeDefinition->getIdentifier();
        }

        return false;
    }

    public function getGroupName(): string
    {
        return $this->attributeDefinition->getGroup()->getName();
    }
}
