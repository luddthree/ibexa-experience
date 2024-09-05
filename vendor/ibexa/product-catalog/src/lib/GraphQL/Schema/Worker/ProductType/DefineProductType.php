<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\GraphQL\Schema\Worker\ProductType;

use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\GraphQL\Schema\Builder;
use Ibexa\GraphQL\Schema\Builder\Input\Type;
use Ibexa\GraphQL\Schema\Worker;
use Ibexa\ProductCatalog\GraphQL\Schema\Worker\Base;

final class DefineProductType extends Base implements Worker
{
    /**
     * @param array<mixed> $args
     */
    public function work(Builder $schema, array $args): void
    {
        $schema->addType(new Type(
            $this->getProductTypeName($args),
            'object',
            ['inherits' => 'BaseProductType']
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
            !$schema->hasType($this->getProductTypeName($args));
    }

    /**
     * @param array<mixed> $args
     */
    private function getProductTypeName(array $args): string
    {
        return $this
            ->getNameHelper()
            ->getProductTypeName(
                $args[self::PRODUCT_TYPE_DEFINITION_IDENTIFIER]
            );
    }
}
