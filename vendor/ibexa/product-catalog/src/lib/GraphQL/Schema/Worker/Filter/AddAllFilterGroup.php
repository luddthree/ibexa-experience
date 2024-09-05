<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\GraphQL\Schema\Worker\Filter;

use Ibexa\GraphQL\Schema\Builder;
use Ibexa\GraphQL\Schema\Worker;
use Ibexa\ProductCatalog\GraphQL\Schema\Worker\FilterAware;

final class AddAllFilterGroup extends FilterAware implements Worker
{
    private const ALL_FIELD_ID = 'all';

    /**
     * @param array<mixed> $args
     */
    public function work(Builder $schema, array $args): void
    {
        $schema->addFieldToType(
            self::DOMAIN_PRODUCTS_DEFINITION_IDENTIFIER,
            new Builder\Input\Field(
                self::ALL_FIELD_ID,
                self::BASE_PRODUCT_CONNECTION_TYPE,
                [
                    'resolve' => '@=query("ProductsList", args)',
                    'argsBuilder' => 'Relay::Connection',
                ]
            )
        );

        $this->addFilters($schema, self::DOMAIN_PRODUCTS_DEFINITION_IDENTIFIER, self::ALL_FIELD_ID);
        $this->addSortClauses($schema, self::DOMAIN_PRODUCTS_DEFINITION_IDENTIFIER, self::ALL_FIELD_ID);
    }

    /**
     * @param array<mixed> $args
     */
    public function canWork(Builder $schema, array $args): bool
    {
        return
            $schema->hasType(self::DOMAIN_PRODUCTS_DEFINITION_IDENTIFIER) &&
            !$schema->hasTypeWithField(
                self::DOMAIN_PRODUCTS_DEFINITION_IDENTIFIER,
                self::ALL_FIELD_ID
            );
    }
}
