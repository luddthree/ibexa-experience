<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\ContentTypeAwareProductTypeInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\ProductCatalog\Exception\MissingSpecificationException;
use Ibexa\ProductCatalog\FieldType\ProductSpecification\Type as ProductSpecificationFieldType;

/**
 * @internal
 */
final class ProductSpecificationLocator
{
    private ContentService $contentService;

    public function __construct(ContentService $contentService)
    {
        $this->contentService = $contentService;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function findField(ProductInterface $product): Field
    {
        if (!$product instanceof ContentAwareProductInterface) {
            throw new InvalidArgumentException(
                'product',
                'must be an instance of ' . ContentAwareProductInterface::class
            );
        }

        $content = $product->getContent();
        $contentWithAllTranslations = $this->contentService->loadContent($content->id, Language::ALL);
        $languageCode = $content->getVersionInfo()->getContentInfo()->getMainLanguageCode();
        foreach ($contentWithAllTranslations->getFieldsByLanguage($languageCode) as $field) {
            if ($field->fieldTypeIdentifier === ProductSpecificationFieldType::FIELD_TYPE_IDENTIFIER) {
                return $field;
            }
        }

        throw new MissingSpecificationException(
            'Missing Product Specification field in the Content'
        );
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function findFieldDefinition(ProductTypeInterface $productType): FieldDefinition
    {
        if (!$productType instanceof ContentTypeAwareProductTypeInterface) {
            throw new InvalidArgumentException(
                'product type',
                'must be an instance of ' . ContentTypeAwareProductTypeInterface::class
            );
        }

        $contentType = $productType->getContentType();
        $fieldDefinition = $contentType->getFirstFieldDefinitionOfType(
            ProductSpecificationFieldType::FIELD_TYPE_IDENTIFIER
        );

        if ($fieldDefinition === null) {
            throw new MissingSpecificationException(
                'Missing Product Specification field type in the content type'
            );
        }

        return $fieldDefinition;
    }
}
