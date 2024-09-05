<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\FieldType;

use Ibexa\Contracts\Core\Persistence\Content\FieldTypeConstraints;
use Ibexa\Contracts\Core\Persistence\Content\FieldValue;
use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition;
use Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldDefinition;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldValue;

/**
 * @internal
 */
final class MeasurementValueConverter implements Converter
{
    public function toStorageValue(FieldValue $value, StorageFieldValue $storageFieldValue): void
    {
        $storageFieldValue->sortKeyString = (string)$value->sortKey;
        $storageFieldValue->dataInt = (int)($value->data['inputType'] ?? MeasurementType::INPUT_TYPE_SIMPLE_INPUT);
    }

    public function toFieldValue(StorageFieldValue $value, FieldValue $fieldValue): void
    {
        $fieldValue->sortKey = $value->sortKeyString;
        $fieldValue->data['inputType'] = $value->dataInt;
    }

    public function toStorageFieldDefinition(
        FieldDefinition $fieldDef,
        StorageFieldDefinition $storageDef
    ): void {
        /** @var array<string, array<string, string>> $validators */
        $validators = $fieldDef->fieldTypeConstraints->validators;

        $storageDef->dataText1 = $validators['MeasurementValidator']['measurementType'] ?? null;
        $storageDef->dataText2 = $validators['MeasurementValidator']['measurementUnit'] ?? null;
        $storageDef->dataInt1 = (int)($validators['MeasurementValidator']['inputType'] ?? MeasurementType::INPUT_TYPE_SIMPLE_INPUT);
        $storageDef->dataFloat1 = $validators['MeasurementValidator']['minimum'] ?? null;
        $storageDef->dataFloat2 = $validators['MeasurementValidator']['maximum'] ?? null;

        if ($storageDef->dataInt1 === MeasurementType::INPUT_TYPE_SIMPLE_INPUT) {
            $storageDef->dataText3 = $validators['MeasurementValidator']['sign'] ?? null;
            $storageDef->dataFloat3 = $validators['MeasurementValidator']['defaultValue'] ?? null;
        } elseif ($storageDef->dataInt1 === MeasurementType::INPUT_TYPE_RANGE) {
            $storageDef->dataFloat3 = $validators['MeasurementValidator']['defaultRangeMinimumValue'] ?? null;
            $storageDef->dataFloat4 = $validators['MeasurementValidator']['defaultRangeMaximumValue'] ?? null;
        }
    }

    public function toFieldDefinition(
        StorageFieldDefinition $storageDef,
        FieldDefinition $fieldDef
    ): void {
        $measurementValidator = [
            'measurementType' => $storageDef->dataText1,
            'measurementUnit' => $storageDef->dataText2,
            'inputType' => $storageDef->dataInt1,
            'minimum' => $storageDef->dataFloat1 ?? null,
            'maximum' => $storageDef->dataFloat2 ?? null,
        ];

        if ($measurementValidator['inputType'] === MeasurementType::INPUT_TYPE_SIMPLE_INPUT) {
            $measurementValidator['sign'] = $storageDef->dataText3 ?? null;
            $measurementValidator['defaultValue'] = $storageDef->dataFloat3 ?? null;

            if (null !== $storageDef->dataFloat3) {
                $fieldDef->defaultValue = new FieldValue();
                $fieldDef->defaultValue->data = [
                    'measurementType' => $storageDef->dataText1,
                    'measurementUnit' => $storageDef->dataText2,
                    'inputType' => $storageDef->dataInt1,
                    'value' => $storageDef->dataFloat3,
                ];
            }
        } elseif ($measurementValidator['inputType'] === MeasurementType::INPUT_TYPE_RANGE) {
            $measurementValidator['defaultRangeMinimumValue'] = $storageDef->dataFloat3 ?? null;
            $measurementValidator['defaultRangeMaximumValue'] = $storageDef->dataFloat4 ?? null;

            if (null !== $storageDef->dataFloat3 && null !== $storageDef->dataFloat4) {
                $fieldDef->defaultValue = new FieldValue();
                $fieldDef->defaultValue->data = [
                    'measurementType' => $storageDef->dataText1,
                    'measurementUnit' => $storageDef->dataText2,
                    'inputType' => $storageDef->dataInt1,
                    'measurementRangeMinimumValue' => $storageDef->dataFloat3 ?? null,
                    'measurementRangeMaximumValue' => $storageDef->dataFloat4 ?? null,
                ];
            }
        }

        $fieldDef->fieldTypeConstraints = new FieldTypeConstraints(
            [
                'validators' => [
                    'MeasurementValidator' => $measurementValidator,
                ],
            ]
        );
    }

    public function getIndexColumn(): string
    {
        return 'sort_key_string';
    }
}
