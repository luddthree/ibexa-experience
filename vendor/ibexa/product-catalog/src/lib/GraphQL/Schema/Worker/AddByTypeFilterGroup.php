<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\GraphQL\Schema\Worker;

use Ibexa\GraphQL\Schema\Builder;
use Ibexa\GraphQL\Schema\Worker;

final class AddByTypeFilterGroup extends Base implements Worker
{
    public const BY_TYPE_GROUP_DEFINITION_IDENTIFIER = 'FilterByTypeGroup';

    /**
     * @param array<mixed> $args
     */
    public function work(Builder $schema, array $args): void
    {
        $schema->addType(
            new Builder\Input\Type(
                self::BY_TYPE_GROUP_DEFINITION_IDENTIFIER,
                'object'
            )
        );

        $schema->addFieldToType(
            self::DOMAIN_PRODUCTS_DEFINITION_IDENTIFIER,
            new Builder\Input\Field(
                'byType',
                self::BY_TYPE_GROUP_DEFINITION_IDENTIFIER,
                ['resolve' => []]
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
            !$schema->hasType(self::BY_TYPE_GROUP_DEFINITION_IDENTIFIER);
    }
}
