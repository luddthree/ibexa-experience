<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\GraphQL\Schema\Worker\ProductType;

use Ibexa\GraphQL\Schema\Builder;
use Ibexa\GraphQL\Schema\Worker;
use Ibexa\ProductCatalog\GraphQL\Schema\Worker\Base;

final class DefineProductTypes extends Base implements Worker
{
    /**
     * @param array<mixed> $args
     */
    public function work(Builder $schema, array $args): void
    {
        $schema->addType(new Builder\Input\Type(
            self::DOMAIN_PRODUCT_TYPES_DEFINITION_IDENTIFIER,
            'object'
        ));
    }

    /**
     * @param array<mixed> $args
     */
    public function canWork(Builder $schema, array $args): bool
    {
        return
            !$schema->hasType(self::DOMAIN_PRODUCT_TYPES_DEFINITION_IDENTIFIER) &&
            $schema->hasType(self::DOMAIN_PRODUCTS_DEFINITION_IDENTIFIER);
    }
}
