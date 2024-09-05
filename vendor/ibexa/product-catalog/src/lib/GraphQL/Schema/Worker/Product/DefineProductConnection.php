<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\GraphQL\Schema\Worker\Product;

use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\GraphQL\Schema\Builder;
use Ibexa\GraphQL\Schema\Builder\Input\Type;
use Ibexa\GraphQL\Schema\Worker;
use Ibexa\ProductCatalog\GraphQL\Schema\Worker\Base;

final class DefineProductConnection extends Base implements Worker
{
    /**
     * @param array<mixed> $args
     */
    public function work(Builder $schema, array $args): void
    {
        $schema->addType(new Type(
            $this->getConnectionTypeName($args),
            'relay-connection',
            [
                'nodeType' => $this->getTypeName($args),
                'connectionFields' => [
                    'sliceSize' => ['type' => 'Int!'],
                    'orderBy' => ['type' => 'String'],
                ],
            ]
        ));
    }

    /**
     * @param array<mixed> $args
     */
    public function canWork(Builder $schema, array $args): bool
    {
        return
            isset($args[self::PRODUCT_TYPE_DEFINITION_IDENTIFIER]) &&
            $args[self::PRODUCT_TYPE_DEFINITION_IDENTIFIER] instanceof ProductTypeInterface &&
            !$schema->hasType($this->getConnectionTypeName($args));
    }

    /**
     * @param array<mixed> $args
     */
    private function getConnectionTypeName(array $args): string
    {
        return $this
            ->getNameHelper()
            ->getProductConnectionName(
                $args[self::PRODUCT_TYPE_DEFINITION_IDENTIFIER]
            );
    }

    /**
     * @param array<mixed> $args
     */
    private function getTypeName(array $args): string
    {
        return $this
            ->getNameHelper()
            ->getProductName(
                $args[self::PRODUCT_TYPE_DEFINITION_IDENTIFIER]
            );
    }
}
