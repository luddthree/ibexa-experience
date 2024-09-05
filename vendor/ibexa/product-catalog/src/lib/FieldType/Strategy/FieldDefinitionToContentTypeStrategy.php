<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\FieldType\Strategy;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Contracts\ProductCatalog\ProductTypeServiceInterface;
use Ibexa\FieldTypeMatrix\FieldType\Mapper\FieldTypeToContentTypeStrategyInterface;

final class FieldDefinitionToContentTypeStrategy implements FieldTypeToContentTypeStrategyInterface
{
    private ProductTypeServiceInterface $productTypeService;

    public function __construct(
        ProductTypeServiceInterface $productTypeService
    ) {
        $this->productTypeService = $productTypeService;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function findContentTypeOf(FieldDefinition $fieldDefinition): ?ContentType
    {
        /** @var \Ibexa\ProductCatalog\Local\Repository\Values\ProductType $productType */
        foreach ($this->productTypeService->findProductTypes()->getProductTypes() as $productType) {
            $contentType = $productType->getContentType();
            $foundFieldDefinition = $contentType->getFieldDefinition($fieldDefinition->identifier);
            if ($foundFieldDefinition === null) {
                continue;
            }
            if ($foundFieldDefinition->id === $fieldDefinition->id) {
                return $contentType;
            }
        }

        return null;
    }
}
