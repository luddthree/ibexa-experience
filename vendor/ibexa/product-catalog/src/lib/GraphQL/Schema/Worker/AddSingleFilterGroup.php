<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\GraphQL\Schema\Worker;

use Ibexa\GraphQL\Schema\Builder;
use Ibexa\GraphQL\Schema\Worker;

final class AddSingleFilterGroup extends Base implements Worker
{
    private const SINGLE_FIELD_ID = 'single';

    /**
     * @param array<mixed> $args
     */
    public function work(Builder $schema, array $args): void
    {
        $schema->addFieldToType(
            self::DOMAIN_PRODUCTS_DEFINITION_IDENTIFIER,
            new Builder\Input\Field(
                self::SINGLE_FIELD_ID,
                Base::BASE_PRODUCT_TYPE,
                [
                    'resolve' => '@=query("Product", args)',
                    'argsBuilder' => 'Relay::Connection',
                ]
            )
        );

        $schema->addArgToField(
            self::DOMAIN_PRODUCTS_DEFINITION_IDENTIFIER,
            self::SINGLE_FIELD_ID,
            new Builder\Input\Arg(
                'code',
                'String!',
                ['description' => 'A "code" filter for a single Product']
            )
        );
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
                self::SINGLE_FIELD_ID
            );
    }
}
