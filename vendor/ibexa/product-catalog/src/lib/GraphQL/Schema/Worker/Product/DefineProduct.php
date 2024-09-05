<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\GraphQL\Schema\Worker\Product;

use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\GraphQL\Schema\Builder;
use Ibexa\GraphQL\Schema\Worker;
use Ibexa\ProductCatalog\GraphQL\Schema\Worker\Base;

final class DefineProduct extends Base implements Worker
{
    private const ATTRIBUTES_INTERFACE = 'ProductAttributesInterface';
    private const CONTENT_FIELDS_INTERFACE = 'ProductContentFieldsInterface';

    /**
     * @param array<mixed> $args
     */
    public function work(Builder $schema, array $args): void
    {
        $productIdentifier = $this->getProductName($args);

        $schema->addType(new Builder\Input\Type(
            $productIdentifier,
            'object',
            ['inherits' => 'BaseProduct']
        ));

        $this->defineAttributes($productIdentifier, $schema, $args);
        $this->defineContentFields($productIdentifier, $schema, $args);
    }

    /**
     * @param array<mixed> $args
     */
    public function canWork(Builder $schema, array $args): bool
    {
        return
            isset($args[self::PRODUCT_TYPE_DEFINITION_IDENTIFIER]) &&
            $args[self::PRODUCT_TYPE_DEFINITION_IDENTIFIER] instanceof ProductTypeInterface &&
            !$schema->hasType($this->getProductName($args));
    }

    /**
     * @param array<mixed> $args
     */
    private function defineAttributes(string $productIdentifier, Builder $schema, array $args): void
    {
        $attributeTypeIdentifier = $this->getProductAttributes($args);

        $schema->addType(new Builder\Input\Type(
            $attributeTypeIdentifier,
            'object',
            [
                'inherits' => self::ATTRIBUTES_INTERFACE,
                'interfaces' => self::ATTRIBUTES_INTERFACE,
            ]
        ));

        $schema->addFieldToType(
            $attributeTypeIdentifier,
            new Builder\Input\Field(
                '_all',
                '[AttributeInterface]',
                [
                    'resolve' => '@=query("AttributesByProduct", value)',
                ]
            )
        );

        $schema->addFieldToType(
            $productIdentifier,
            new Builder\Input\Field(
                'attributes',
                $attributeTypeIdentifier,
                ['resolve' => '@=value']
            )
        );
    }

    /**
     * @param array<mixed> $args
     */
    private function defineContentFields(string $productIdentifier, Builder $schema, array $args): void
    {
        $contentFieldsIdentifier = $this->getTypeContentFields($args);

        $schema->addType(new Builder\Input\Type(
            $contentFieldsIdentifier,
            'object',
            [
                'inherits' => self::CONTENT_FIELDS_INTERFACE,
                'interfaces' => self::CONTENT_FIELDS_INTERFACE,
            ]
        ));

        $schema->addFieldToType(
            $contentFieldsIdentifier,
            new Builder\Input\Field(
                '_all',
                '[ProductContentField]',
                [
                    'resolve' => '@=query("ContentFieldsByProduct", value)',
                ]
            )
        );

        $schema->addFieldToType(
            $productIdentifier,
            new Builder\Input\Field(
                'fields',
                $contentFieldsIdentifier,
                ['resolve' => '@=value']
            )
        );
    }

    /**
     * @param array<mixed> $args
     */
    private function getProductName(array $args): string
    {
        return $this
            ->getNameHelper()
            ->getProductName(
                $args[self::PRODUCT_TYPE_DEFINITION_IDENTIFIER]
            );
    }

    /**
     * @param array<mixed> $args
     */
    private function getProductAttributes(array $args): string
    {
        return $this
            ->getNameHelper()
            ->getProductAttributes(
                $args[self::PRODUCT_TYPE_DEFINITION_IDENTIFIER]
            );
    }

    /**
     * @param array<mixed> $args
     */
    private function getTypeContentFields(array $args): string
    {
        return $this
            ->getNameHelper()
            ->getProductContentFields(
                $args[self::PRODUCT_TYPE_DEFINITION_IDENTIFIER]
            );
    }
}
