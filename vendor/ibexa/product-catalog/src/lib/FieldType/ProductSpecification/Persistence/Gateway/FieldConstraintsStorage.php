<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\FieldType\ProductSpecification\Persistence\Gateway;

use Ibexa\Contracts\Core\FieldType\FieldConstraintsStorage as FieldConstraintsStorageInterface;
use Ibexa\Contracts\Core\Persistence\Content\FieldTypeConstraints;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\ProductCatalog\RegionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Region\RegionQuery;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\Attribute\Handler as AttributeHandler;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeDefinition\HandlerInterface as AttributeDefinitionHandler;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeDefinitionAssignment\HandlerInterface as AttributeDefinitionAssignmentHandler;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeGroup\HandlerInterface as AttributeGroupHandler;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductTypeSettings\HandlerInterface as ProductTypeSettingsHandler;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductTypeVatCategory\HandlerInterface as VatCategoryAssignmentHandler;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeDefinitionAssignmentCreateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Values\ProductTypeSetting;
use Ibexa\ProductCatalog\Local\Persistence\Values\ProductTypeSettingCreateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Values\ProductTypeVatCategoryCreateStruct;

final class FieldConstraintsStorage implements FieldConstraintsStorageInterface
{
    private AttributeDefinitionAssignmentHandler $assignmentHandler;

    private AttributeDefinitionHandler $attributeDefinitionHandler;

    private AttributeGroupHandler $attributeGroupHandler;

    private ProductTypeSettingsHandler $productTypeSettingsHandler;

    private RegionServiceInterface $regionService;

    private VatCategoryAssignmentHandler $vatCategoryAssignmentHandler;

    private AttributeHandler $attributeHandler;

    public function __construct(
        AttributeDefinitionAssignmentHandler $attributeDefinitionAssignmentHandler,
        AttributeDefinitionHandler $attributeDefinitionHandler,
        AttributeGroupHandler $attributeGroupHandler,
        ProductTypeSettingsHandler $productTypeSettingsHandler,
        AttributeHandler $attributeHandler,
        RegionServiceInterface $regionService,
        VatCategoryAssignmentHandler $vatCategoryAssignmentHandler
    ) {
        $this->assignmentHandler = $attributeDefinitionAssignmentHandler;
        $this->attributeDefinitionHandler = $attributeDefinitionHandler;
        $this->attributeGroupHandler = $attributeGroupHandler;
        $this->productTypeSettingsHandler = $productTypeSettingsHandler;
        $this->regionService = $regionService;
        $this->vatCategoryAssignmentHandler = $vatCategoryAssignmentHandler;
        $this->attributeHandler = $attributeHandler;
    }

    public function storeFieldConstraintsData(
        int $fieldDefinitionId,
        int $status,
        FieldTypeConstraints $fieldTypeConstraints
    ): void {
        $this->storeAttributeDefinitions($fieldDefinitionId, $status, $fieldTypeConstraints);
        $this->storeRegionData($fieldDefinitionId, $status, $fieldTypeConstraints);
        $this->storeProductTypeSetting($fieldDefinitionId, $status, $fieldTypeConstraints);
    }

    public function getFieldConstraintsData(
        int $fieldDefinitionId,
        int $status
    ): FieldTypeConstraints {
        $constraints = new FieldTypeConstraints();
        $constraints->fieldSettings['attributes_definitions'] = $this->getAttributeDefinitions($fieldDefinitionId, $status);
        $constraints->fieldSettings['regions'] = $this->getRegionSettings($fieldDefinitionId);
        $constraints->fieldSettings['is_virtual'] = $this->isVirtual($fieldDefinitionId, $status);

        return $constraints;
    }

    public function deleteFieldConstraintsData(
        int $fieldDefinitionId,
        int $status
    ): void {
        $this->assignmentHandler->deleteByFieldDefinitionId($fieldDefinitionId, $status);
        $this->productTypeSettingsHandler->deleteByFieldDefinitionId($fieldDefinitionId, $status);
    }

    public function publishFieldConstraintsData(int $fieldDefinitionId): void
    {
        $this->assignmentHandler->publish($fieldDefinitionId);
        $this->productTypeSettingsHandler->publish($fieldDefinitionId);
    }

    private function storeAttributeDefinitions(int $fieldDefinitionId, int $status, FieldTypeConstraints $fieldTypeConstraints): void
    {
        $currentAssignmentsIds = array_column(
            $this->assignmentHandler->findByFieldDefinitionId($fieldDefinitionId, $status),
            'attributeDefinitionId'
        );

        $this->assignmentHandler->deleteByFieldDefinitionId($fieldDefinitionId, $status);

        $assignments = $fieldTypeConstraints->fieldSettings['attributes_definitions'] ?? [];
        foreach ($assignments as $group) {
            foreach ($group as $assignment) {
                $definition = $this->attributeDefinitionHandler->loadByIdentifier($assignment['attributeDefinition']);

                $createStruct = new AttributeDefinitionAssignmentCreateStruct();
                $createStruct->fieldDefinitionId = $fieldDefinitionId;
                $createStruct->attributeDefinitionId = $definition->id;
                $createStruct->discriminator = $assignment['discriminator'] ?? false;
                $createStruct->required = $assignment['required'] ?? false;

                $this->assignmentHandler->create($createStruct, $status);
            }
        }
        $newAssignments = array_column(
            $this->assignmentHandler->findByFieldDefinitionId($fieldDefinitionId, $status),
            'attributeDefinitionId'
        );

        $attributesToDelete = array_diff($currentAssignmentsIds, $newAssignments);
        foreach ($attributesToDelete as $attributeDefinitionId) {
            $this->attributeHandler->deleteByAttributeDefinition($attributeDefinitionId);
        }
    }

    private function storeRegionData(int $fieldDefinitionId, int $status, FieldTypeConstraints $fieldTypeConstraints): void
    {
        $this->vatCategoryAssignmentHandler->deleteByFieldDefinitionId($fieldDefinitionId, $status);

        /** @var array<string, array{region_identifier: string, vat_category_identifier: string|null}> $regions */
        $regions = $fieldTypeConstraints->fieldSettings['regions'];

        foreach ($regions as $regionData) {
            if ($regionData['vat_category_identifier'] === null) {
                continue;
            }

            $assignment = new ProductTypeVatCategoryCreateStruct(
                $fieldDefinitionId,
                $status,
                $regionData['region_identifier'],
                $regionData['vat_category_identifier'],
            );

            $this->vatCategoryAssignmentHandler->create($assignment);
        }
    }

    private function storeProductTypeSetting(
        int $fieldDefinitionId,
        int $status,
        FieldTypeConstraints $fieldTypeConstraints
    ): void {
        $this->productTypeSettingsHandler->deleteByFieldDefinitionId($fieldDefinitionId, $status);

        $this->productTypeSettingsHandler->create(
            new ProductTypeSettingCreateStruct(
                $fieldDefinitionId,
                $status,
                (bool)($fieldTypeConstraints->fieldSettings['is_virtual'] ?? false)
            )
        );
    }

    /**
     * @phpstan-return array<
     *     string,
     *     array<
     *         array{attributeDefinition: string, discriminator: bool, required: bool}
     *     >
     * >
     */
    private function getAttributeDefinitions(int $fieldDefinitionId, int $status): array
    {
        $attributesDefinitions = [];

        $assignments = $this->assignmentHandler->findByFieldDefinitionId($fieldDefinitionId, $status);
        foreach ($assignments as $assignment) {
            $definition = $this->attributeDefinitionHandler->load($assignment->attributeDefinitionId);

            $group = $this->attributeGroupHandler->load($definition->attributeGroupId);
            if (!array_key_exists($group->identifier, $attributesDefinitions)) {
                $attributesDefinitions[$group->identifier] = [];
            }

            $attributesDefinitions[$group->identifier][] = [
                'attributeDefinition' => $definition->identifier,
                'discriminator' => $assignment->discriminator,
                'required' => $assignment->required,
            ];
        }

        return $attributesDefinitions;
    }

    /**
     * @return array<string, array{region_identifier: string, vat_category_identifier: string|null}>
     */
    private function getRegionSettings(int $fieldDefinitionId): array
    {
        $regions = $this->getRegions();

        $assignments = $this->vatCategoryAssignmentHandler->findBy([
            'field_definition_id' => $fieldDefinitionId,
        ]);

        foreach ($assignments as $assignment) {
            $regionIdentifier = $assignment->getRegion();

            if (!array_key_exists($regionIdentifier, $regions)) {
                // Skip regions which no longer exist
                continue;
            }

            $regionSettings = $regions[$regionIdentifier];

            $vatCategoryIdentifier = $assignment->getVatCategory();
            $regionSettings['vat_category_identifier'] = $vatCategoryIdentifier;
            $regions[$regionIdentifier] = $regionSettings;
        }

        return $regions;
    }

    /**
     * @return array<string, array{region_identifier: string, vat_category_identifier: string|null}> array indexed by region identifiers
     */
    private function getRegions(): array
    {
        $regions = [];
        $query = new RegionQuery(null, [], null);
        foreach ($this->regionService->findRegions($query) as $region) {
            $identifier = $region->getIdentifier();
            $regions[$identifier] = [
                'region_identifier' => $identifier,
                'vat_category_identifier' => null,
            ];
        }

        return $regions;
    }

    private function isVirtual(int $fieldDefinitionId, int $status): bool
    {
        $setting = $this->getProductTypeSetting($fieldDefinitionId, $status);

        return null !== $setting && $setting->isVirtual();
    }

    private function getProductTypeSetting(int $fieldDefinitionId, int $status): ?ProductTypeSetting
    {
        try {
            return $this->productTypeSettingsHandler->findByFieldDefinitionId($fieldDefinitionId, $status);
        } catch (NotFoundException $exception) {
            return null;
        }
    }
}
