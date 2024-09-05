<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FieldTypePage\FieldType\LandingPage;

use Ibexa\Contracts\Core\Persistence\Content\FieldValue;
use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Page;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Zone;
use Ibexa\Core\FieldType\FieldSettings;
use Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldDefinition;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldValue;
use Ibexa\FieldTypePage\FieldType\LandingPage\Converter\PageConverter;
use Ibexa\FieldTypePage\FieldType\LandingPage\Definition\LayoutDefinition;
use Ibexa\FieldTypePage\FieldType\LandingPage\Mapper\LandingPageFormMapper;
use Ibexa\FieldTypePage\Registry\LayoutDefinitionRegistry;

/**
 * Converter for field values in legacy storage.
 */
class LegacyConverter implements Converter
{
    /** @var \Ibexa\FieldTypePage\Registry\LayoutDefinitionRegistry */
    private $layoutDefinitionRegistry;

    /** @var \Ibexa\FieldTypePage\FieldType\LandingPage\Converter\PageConverter */
    private $converter;

    public function __construct(
        LayoutDefinitionRegistry $layoutDefinitionRegistry,
        PageConverter $converter
    ) {
        $this->layoutDefinitionRegistry = $layoutDefinitionRegistry;
        $this->converter = $converter;
    }

    /**
     * Converts data from $value to $storageFieldValue.
     *
     * @param \Ibexa\Contracts\Core\Persistence\Content\FieldValue $value
     * @param \Ibexa\Core\Persistence\Legacy\Content\StorageFieldValue $storageFieldValue
     */
    public function toStorageValue(FieldValue $value, StorageFieldValue $storageFieldValue)
    {
        $storageFieldValue->dataInt = $value->data;
    }

    /**
     * Converts data from $value to $fieldValue.
     *
     * @param \Ibexa\Core\Persistence\Legacy\Content\StorageFieldValue $value
     * @param \Ibexa\Contracts\Core\Persistence\Content\FieldValue $fieldValue
     */
    public function toFieldValue(StorageFieldValue $value, FieldValue $fieldValue)
    {
        $fieldValue->data = $value->dataInt;
    }

    /**
     * Converts field definition data in $fieldDef into $storageFieldDef.
     *
     * @param \Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition $fieldDef
     * @param \Ibexa\Core\Persistence\Legacy\Content\StorageFieldDefinition $storageDef
     */
    public function toStorageFieldDefinition(FieldDefinition $fieldDef, StorageFieldDefinition $storageDef)
    {
        $fieldSettings = $fieldDef->fieldTypeConstraints->fieldSettings;

        $storageDef->dataText5 = json_encode($fieldSettings);
    }

    /**
     * Converts field definition data in $storageDef into $fieldDef.
     *
     * @param \Ibexa\Core\Persistence\Legacy\Content\StorageFieldDefinition $storageDef
     * @param \Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition $fieldDef
     */
    public function toFieldDefinition(StorageFieldDefinition $storageDef, FieldDefinition $fieldDef)
    {
        $settings = [
            'availableBlocks' => null,
            'availableLayouts' => null,
            'editorMode' => LandingPageFormMapper::PAGE_VIEW_MODE,
        ];

        if ($storageDef->dataText5 !== null) {
            $settings = array_merge(
                $settings,
                json_decode($storageDef->dataText5, true)
            );
        }

        $fieldDef->fieldTypeConstraints->fieldSettings = new FieldSettings($settings);

        $defaultLayout = $this->layoutDefinitionRegistry->getDefaultLayout($settings['availableLayouts']);
        $fieldDef->defaultValue->externalData = $this->converter->toArray($this->buildDefaultPage($defaultLayout));
    }

    /**
     * Returns the name of the index column in the attribute table.
     *
     * Returns the name of the index column the datatype uses, which is either
     * "sort_key_int" or "sort_key_string". This column is then used for
     * filtering and sorting for this type.
     *
     * If the indexing is not supported, this method must return false.
     *
     * @return string|false
     */
    public function getIndexColumn()
    {
        return false;
    }

    private function buildDefaultPage(LayoutDefinition $defaultLayoutDefinition): Page
    {
        $zones = [];
        foreach ($defaultLayoutDefinition->getZones() as $identifier => $zoneDefinition) {
            $zones[] = new Zone($identifier, $zoneDefinition['name']);
        }

        return new Page(
            $defaultLayoutDefinition->getId(),
            $zones
        );
    }
}

class_alias(LegacyConverter::class, 'EzSystems\EzPlatformPageFieldType\FieldType\LandingPage\LegacyConverter');
