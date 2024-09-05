<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\GraphQL\Schema\Worker\Attribute;

use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\GraphQL\Schema\Builder;
use Ibexa\GraphQL\Schema\Worker;
use Ibexa\ProductCatalog\GraphQL\Schema\Worker\Base;
use Overblog\GraphQLBundle\Resolver\TypeResolver;

final class AddProductAttributes extends Base implements Worker
{
    private TypeResolver $typeResolver;

    public function __construct(TypeResolver $typeResolver)
    {
        $this->typeResolver = $typeResolver;
    }

    /**
     * @param array<mixed> $args
     */
    public function work(Builder $schema, array $args): void
    {
        $typeIdentifier = $this->getTypeAttributes($args);
        $attribute = $args[self::ATTRIBUTE_DEFINITION_IDENTIFIER];
        $attributeIdentifier = $attribute->getIdentifier();

        $attributeTypeName = $this->getAttributeTypeDefinition(
            $attribute->getType()->getIdentifier()
        );

        $resolvedAttributeTypeName = $this->typeResolver->hasSolution($attributeTypeName)
            ? $attributeTypeName
            : 'UntypedAttribute';

        $schema->addFieldToType(
            $typeIdentifier,
            new Builder\Input\Field(
                $attributeIdentifier,
                $resolvedAttributeTypeName,
                [
                    'resolve' => sprintf(
                        '@=query("AttributeByIdentifier", value, "%s")',
                        $attributeIdentifier
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
            isset(
                $args[self::PRODUCT_TYPE_DEFINITION_IDENTIFIER],
                $args[self::ATTRIBUTE_DEFINITION_IDENTIFIER]
            ) &&
            $args[self::PRODUCT_TYPE_DEFINITION_IDENTIFIER] instanceof ProductTypeInterface &&
            $args[self::ATTRIBUTE_DEFINITION_IDENTIFIER] instanceof AttributeDefinitionInterface &&
            $schema->hasType($this->getTypeAttributes($args));
    }

    private function getAttributeTypeDefinition(string $attributeTypeIdentifier): string
    {
        return $this->getNameHelper()->getAttributeType($attributeTypeIdentifier);
    }

    /**
     * @param array<mixed> $args
     */
    private function getTypeAttributes(array $args): string
    {
        return $this
            ->getNameHelper()
            ->getProductAttributes(
                $args[self::PRODUCT_TYPE_DEFINITION_IDENTIFIER]
            );
    }
}
