<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\GraphQL\Schema\Worker\Filter;

use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\GraphQL\Schema\Builder;
use Ibexa\GraphQL\Schema\Worker;
use Ibexa\ProductCatalog\GraphQL\Schema\Worker\AddByTypeFilterGroup;
use Ibexa\ProductCatalog\GraphQL\Schema\Worker\FilterAware;

final class FilterByType extends FilterAware implements Worker
{
    /**
     * @param array<mixed> $args
     */
    public function work(Builder $schema, array $args): void
    {
        $productType = $args[self::PRODUCT_TYPE_DEFINITION_IDENTIFIER];
        $productTypePluralName = $this->getProductTypePluralName($args);
        $productTypeConnectionName = $this
            ->getNameHelper()
            ->getProductConnectionName($productType);

        $schema->addFieldToType(
            AddByTypeFilterGroup::BY_TYPE_GROUP_DEFINITION_IDENTIFIER,
            new Builder\Input\Field(
                $productTypePluralName,
                $productTypeConnectionName,
                [
                    'resolve' => sprintf(
                        '@=query("ProductOfType", "%s", args)',
                        $productType->getIdentifier()
                    ),
                    'argsBuilder' => 'Relay::Connection',
                ]
            )
        );

        $this->addFilters($schema, AddByTypeFilterGroup::BY_TYPE_GROUP_DEFINITION_IDENTIFIER, $productTypePluralName);
        $this->addSortClauses($schema, AddByTypeFilterGroup::BY_TYPE_GROUP_DEFINITION_IDENTIFIER, $productTypePluralName);
    }

    /**
     * @param array<mixed> $args
     */
    public function canWork(Builder $schema, array $args): bool
    {
        return
            $schema->hasType(AddByTypeFilterGroup::BY_TYPE_GROUP_DEFINITION_IDENTIFIER) &&
            isset($args[self::PRODUCT_TYPE_DEFINITION_IDENTIFIER]) &&
            $args[self::PRODUCT_TYPE_DEFINITION_IDENTIFIER] instanceof ProductTypeInterface &&
            !$schema->hasTypeWithField(
                AddByTypeFilterGroup::BY_TYPE_GROUP_DEFINITION_IDENTIFIER,
                $this->getProductTypePluralName($args)
            );
    }

    /**
     * @param array<mixed> $args
     */
    private function getProductTypePluralName(array $args): string
    {
        return $this
            ->getNameHelper()
            ->getProductTypeFieldPlural(
                $args[self::PRODUCT_TYPE_DEFINITION_IDENTIFIER]
            );
    }
}
