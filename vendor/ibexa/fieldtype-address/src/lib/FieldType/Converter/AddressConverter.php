<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypeAddress\FieldType\Converter;

use Ibexa\Contracts\Core\FieldType\FieldType;
use Ibexa\Contracts\Core\Persistence\Content\FieldValue;
use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition;
use Ibexa\Core\FieldType\FieldTypeRegistry;
use Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldDefinition;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldValue;

final class AddressConverter implements Converter
{
    private FieldType $fieldType;

    public function __construct(
        FieldTypeRegistry $fieldTypeRegistry,
        string $fieldTypeIdentifier
    ) {
        $this->fieldType = $fieldTypeRegistry->getFieldType(
            $fieldTypeIdentifier
        );
    }

    public function toStorageValue(
        FieldValue $value,
        StorageFieldValue $storageFieldValue
    ): void {
        $address = $value->data ?? [];

        $storageFieldValue->dataText = json_encode($address, JSON_THROW_ON_ERROR);
    }

    public function toFieldValue(
        StorageFieldValue $value,
        FieldValue $fieldValue
    ): void {
        $fieldValue->data = $value->dataText === null ? [] : json_decode($value->dataText, true, 512, JSON_THROW_ON_ERROR);
    }

    public function toStorageFieldDefinition(
        FieldDefinition $fieldDef,
        StorageFieldDefinition $storageDef
    ): void {
        $storageDef->dataText5 = json_encode(
            $fieldDef->fieldTypeConstraints->fieldSettings,
            JSON_THROW_ON_ERROR
        );
    }

    public function toFieldDefinition(
        StorageFieldDefinition $storageDef,
        FieldDefinition $fieldDef
    ): void {
        $defaults = $this->getDefaultSettings($this->fieldType);
        $settings = json_decode(
            $storageDef->dataText5 ?? json_encode($defaults, JSON_THROW_ON_ERROR),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $fieldDef->fieldTypeConstraints->fieldSettings = array_merge(
            $defaults,
            $settings
        );
    }

    public function getIndexColumn(): bool
    {
        return false;
    }

    private function getDefaultSettings(FieldType $fieldType): array
    {
        $settingsSchema = $fieldType->getSettingsSchema();

        return array_combine(
            array_keys($settingsSchema),
            array_column($settingsSchema, 'default')
        );
    }
}
