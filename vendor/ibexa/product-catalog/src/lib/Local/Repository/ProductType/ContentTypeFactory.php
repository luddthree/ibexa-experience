<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\ProductType;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinitionCreateStruct;
use Ibexa\ProductCatalog\FieldType\ProductSpecification\Type as ProductSpecificationFieldType;

final class ContentTypeFactory implements ContentTypeFactoryInterface
{
    private ContentTypeService $contentTypeService;

    public function __construct(ContentTypeService $contentTypeService)
    {
        $this->contentTypeService = $contentTypeService;
    }

    public function createContentTypeCreateStruct(
        string $identifier,
        string $mainLanguageCode,
        iterable $attributes = [],
        bool $isVirtual = false
    ): ContentTypeCreateStruct {
        $createStruct = $this->contentTypeService->newContentTypeCreateStruct($identifier);
        $createStruct->mainLanguageCode = $mainLanguageCode;
        $createStruct->nameSchema = '<name>';
        $createStruct->names = [
            $mainLanguageCode => $this->getInitialProductTypeName($isVirtual),
        ];

        $createStruct->addFieldDefinition($this->createNameField($mainLanguageCode, 10));
        $createStruct->addFieldDefinition($this->createProductSpecificationField($attributes, $isVirtual, $mainLanguageCode, 20));
        $createStruct->addFieldDefinition($this->createDescriptionField($mainLanguageCode, 30));
        $createStruct->addFieldDefinition($this->createImageField($mainLanguageCode, 40));
        $createStruct->addFieldDefinition($this->createCategoryField($mainLanguageCode, 50));

        return $createStruct;
    }

    private function getInitialProductTypeName(bool $isVirtual): string
    {
        return sprintf(
            'New %s Product Type',
            $isVirtual ? 'Virtual' : 'Physical'
        );
    }

    private function createNameField(string $mainLanguageCode, int $position): FieldDefinitionCreateStruct
    {
        $createStruct = $this->contentTypeService->newFieldDefinitionCreateStruct('name', 'ezstring');
        $createStruct->isRequired = true;
        $createStruct->isTranslatable = true;
        $createStruct->isSearchable = true;
        $createStruct->names = [
            $mainLanguageCode => 'Name',
        ];
        $createStruct->position = $position;

        return $createStruct;
    }

    /**
     * @param iterable<int, \Ibexa\Contracts\ProductCatalog\Local\Values\ProductType\AssignAttributeDefinitionStruct> $attributes
     */
    private function createProductSpecificationField(
        iterable $attributes,
        bool $isVirtual,
        string $mainLanguageCode,
        int $position
    ): FieldDefinitionCreateStruct {
        $createStruct = $this->contentTypeService->newFieldDefinitionCreateStruct(
            'product_specification',
            ProductSpecificationFieldType::FIELD_TYPE_IDENTIFIER
        );
        $createStruct->isRequired = true;
        $createStruct->isTranslatable = false;
        $createStruct->names = [
            $mainLanguageCode => 'Product Specification',
        ];

        $fieldSettings = $this->getAttributeDefinitionSettings($attributes);
        $fieldSettings['is_virtual'] = $isVirtual;

        $createStruct->fieldSettings = $fieldSettings;
        $createStruct->position = $position;

        return $createStruct;
    }

    private function createDescriptionField(
        string $mainLanguageCode,
        int $position
    ): FieldDefinitionCreateStruct {
        $createStruct = $this->contentTypeService->newFieldDefinitionCreateStruct('description', 'ezrichtext');
        $createStruct->isRequired = false;
        $createStruct->isTranslatable = true;
        $createStruct->isSearchable = true;
        $createStruct->names = [
            $mainLanguageCode => 'Description',
        ];
        $createStruct->position = $position;

        return $createStruct;
    }

    private function createImageField(
        string $mainLanguageCode,
        int $position
    ): FieldDefinitionCreateStruct {
        $createStruct = $this->contentTypeService->newFieldDefinitionCreateStruct('image', 'ezimageasset');
        $createStruct->isRequired = false;
        $createStruct->isTranslatable = false;
        $createStruct->isSearchable = true;
        $createStruct->isThumbnail = true;
        $createStruct->names = [
            $mainLanguageCode => 'Image',
        ];
        $createStruct->position = $position;

        return $createStruct;
    }

    private function createCategoryField(
        string $mainLanguageCode,
        int $position
    ): FieldDefinitionCreateStruct {
        $createStruct = $this->contentTypeService->newFieldDefinitionCreateStruct(
            'category',
            'ibexa_taxonomy_entry_assignment'
        );

        $createStruct->isRequired = false;
        $createStruct->isTranslatable = false;
        $createStruct->isSearchable = true;
        $createStruct->isThumbnail = false;
        $createStruct->names = [
            $mainLanguageCode => 'Category',
        ];
        $createStruct->fieldSettings = [
            'taxonomy' => 'product_categories',
        ];
        $createStruct->position = $position;

        return $createStruct;
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Local\Values\ProductType\AssignAttributeDefinitionStruct[] $assignments
     *
     * @phpstan-return array{
     *     attributes_definitions: array<
     *         string,
     *         non-empty-array<
     *             int,
     *             array{
     *                 attributeDefinition: string,
     *                 discriminator: bool,
     *                 required: bool
     *             }
     *         >
     *     >,
     *     regions: array{},
     * }
     */
    private function getAttributeDefinitionSettings(iterable $assignments): array
    {
        $fieldSettings = [
            'attributes_definitions' => [],
            'regions' => [],
        ];

        foreach ($assignments as $assignment) {
            $definition = $assignment->getAttributeDefinition();

            $fieldSettings['attributes_definitions'][$definition->getGroup()->getIdentifier()][] = [
                'attributeDefinition' => $definition->getIdentifier(),
                'discriminator' => $assignment->isDiscriminator(),
                'required' => $assignment->isRequired(),
            ];
        }

        return $fieldSettings;
    }
}
