<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\GraphQL\Schema;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\GraphQL\Schema\Domain\BaseNameHelper;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;

final class NameHelper extends BaseNameHelper
{
    public function getProductTypeName(ProductTypeInterface $productType): string
    {
        return ucfirst(
            $this->toCamelCase($productType->getIdentifier())
        ) . 'ProductType';
    }

    public function getProductName(ProductTypeInterface $productType): string
    {
        return ucfirst(
            $this->toCamelCase($productType->getIdentifier())
        ) . 'Product';
    }

    public function getProductConnectionName(ProductTypeInterface $productType): string
    {
        return ucfirst(
            $this->toCamelCase($productType->getIdentifier())
        ) . 'ProductConnection';
    }

    public function getProductField(ProductTypeInterface $productType): string
    {
        return lcfirst($this->toCamelCase($productType->getIdentifier()));
    }

    public function getProductAttributes(ProductTypeInterface $productType): string
    {
        return ucfirst(
            $this->toCamelCase($productType->getIdentifier())
        ) . 'Attributes';
    }

    public function getProductContentFields(ProductTypeInterface $productType): string
    {
        return ucfirst(
            $this->toCamelCase($productType->getIdentifier())
        ) . 'ContentFields';
    }

    public function getProductTypeFieldPlural(ProductTypeInterface $productType): string
    {
        return $this->pluralize(
            lcfirst($this->toCamelCase($productType->getIdentifier()))
        );
    }

    public function getFieldDefinition(FieldDefinition $fieldDefinition): string
    {
        return lcfirst($this->toCamelCase($fieldDefinition->identifier));
    }

    public function getAttributeType(string $attributeTypeIdentifier): string
    {
        return ucfirst(
            $this->toCamelCase($attributeTypeIdentifier)
        ) . 'Attribute';
    }

    public function getMatrixFieldDefinitionType(
        ContentType $contentType,
        FieldDefinition $fieldDefinition
    ): string {
        $caseConverter = new CamelCaseToSnakeCaseNameConverter(null, false);

        return sprintf(
            '%s%sRow',
            $caseConverter->denormalize($contentType->identifier),
            $caseConverter->denormalize($fieldDefinition->identifier)
        );
    }

    public function getMatrixFieldDefinitionInputType(
        ContentType $contentType,
        FieldDefinition $fieldDefinition
    ): string {
        $caseConverter = new CamelCaseToSnakeCaseNameConverter(null, false);

        return sprintf(
            '%s%sRowInput',
            $caseConverter->denormalize($contentType->identifier),
            $caseConverter->denormalize($fieldDefinition->identifier)
        );
    }
}
