<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Seo\FieldType;

use Ibexa\Contracts\Core\Persistence\Content\FieldTypeConstraints;
use Ibexa\Contracts\Core\Persistence\Content\FieldValue;
use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition;
use Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldDefinition;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldValue;
use Ibexa\Seo\Value\SeoTypesValue;
use Ibexa\Seo\Value\SeoTypeValue;

final class SeoValueConverter implements Converter
{
    public function toStorageValue(FieldValue $value, StorageFieldValue $storageFieldValue): void
    {
        /** @var array{value: \Ibexa\Seo\FieldType\SeoValue}|null $data */
        $data = $value->data;

        if (null === $data) {
            return;
        }

        $innerValue = $data['value'];

        if ($innerValue instanceof SeoValue) {
            $innerValue = $innerValue->getSeoTypesValue();
        }

        $storageFieldValue->sortKeyString = (string)$value->sortKey;
        $storageFieldValue->dataText = null !== $innerValue ?
            json_encode($innerValue->getSeoTypesValues(), JSON_THROW_ON_ERROR) :
            '';
    }

    public function toFieldValue(StorageFieldValue $value, FieldValue $fieldValue): void
    {
        /** @var array<string, array{
         *     type: string,
         *     fields: array<string, string>,
         * }> $types
         */
        $types = !empty($value->dataText) ?
            json_decode($value->dataText, true, 512, JSON_THROW_ON_ERROR) :
            [];

        $seoTypes = [];

        foreach ($types as $type) {
            $seoTypes[$type['type']] = new SeoTypeValue($type['type'], $type['fields']);
        }

        $fieldValue->sortKey = $value->sortKeyString;
        $fieldValue->data['value'] = new SeoTypesValue($seoTypes);
    }

    public function toStorageFieldDefinition(
        FieldDefinition $fieldDef,
        StorageFieldDefinition $storageDef
    ): void {
        /** @var array<string, array<string, string>> $fieldSettings */
        $fieldSettings = $fieldDef->fieldTypeConstraints->fieldSettings;

        /** @var \Ibexa\Seo\Value\SeoTypesValue $types */
        $types = $fieldSettings['types'] ?? [];

        if ($types instanceof SeoTypesValue) {
            $types = $types->getTypes();
        }

        $storageDef->dataText5 = json_encode([
            'types' => array_map(static function (SeoTypeValue $field) {
                return [
                    'type' => $field->getType(),
                    'fields' => $field->getFields(),
                ];
            }, $types),
        ], JSON_THROW_ON_ERROR);
    }

    public function toFieldDefinition(
        StorageFieldDefinition $storageDef,
        FieldDefinition $fieldDef
    ): void {
        $storageSeoData = [];

        if (null !== $storageDef->dataText5) {
            /** @var array{
             *     types: array<string, array{
             *          type: string,
             *          fields: array<string, string>,
             *     }>,
             * } $storageSeoData
             */
            $storageSeoData = json_decode(
                $storageDef->dataText5,
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        }

        $seoTypes = [];
        $storageSeoTypes = $storageSeoData['types'] ?? [];

        foreach ($storageSeoTypes as $typeName => $type) {
            $seoTypes[$typeName] = new SeoTypeValue($typeName, $type['fields']);
        }

        $seoTypesValue = new SeoTypesValue($seoTypes);

        $fieldDef->fieldTypeConstraints = new FieldTypeConstraints(
            [
                'fieldSettings' => [
                    'types' => $seoTypesValue,
                ],
            ]
        );
    }

    public function getIndexColumn(): string
    {
        return 'sort_key_string';
    }
}
