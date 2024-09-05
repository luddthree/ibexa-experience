<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\GraphQL\Schema\Worker\ProductType;

use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\GraphQL\Schema\Builder;
use Ibexa\GraphQL\Schema\Builder\Input\Field;
use Ibexa\GraphQL\Schema\Worker;
use Ibexa\ProductCatalog\GraphQL\Schema\Worker\Base;

final class PopulateProductTypes extends Base implements Worker
{
    /**
     * @param array<mixed> $args
     */
    public function work(Builder $schema, array $args): void
    {
        $productType = $args[self::PRODUCT_TYPE_DEFINITION_IDENTIFIER];

        $schema->addFieldToType(
            self::DOMAIN_PRODUCT_TYPES_DEFINITION_IDENTIFIER,
            new Field(
                $this->getProductFieldName($args),
                $this->getNameHelper()->getProductTypeName($productType),
                [
                    'resolve' => sprintf(
                        '@=query("ProductTypeByIdentifier", "%s")',
                        $productType->getIdentifier()
                    ),
                ]
            )
        );
    }

    /**
     * @param array<mixed> $args
     */
    public function canWork(Builder $schema, array $args): bool
    {
        return
            $schema->hasType(self::DOMAIN_PRODUCT_TYPES_DEFINITION_IDENTIFIER) &&
            isset($args[self::PRODUCT_TYPE_DEFINITION_IDENTIFIER]) &&
            $args[self::PRODUCT_TYPE_DEFINITION_IDENTIFIER] instanceof ProductTypeInterface &&
            !$schema->hasTypeWithField(
                self::DOMAIN_PRODUCT_TYPES_DEFINITION_IDENTIFIER,
                $this->getProductFieldName($args)
            );
    }

    /**
     * @param array<mixed> $args
     */
    private function getProductFieldName(array $args): string
    {
        return $this
            ->getNameHelper()
            ->getProductField(
                $args[self::PRODUCT_TYPE_DEFINITION_IDENTIFIER]
            );
    }
}
