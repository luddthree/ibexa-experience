<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Connector\Dam\Persistance\FieldValue;

use Ibexa\Contracts\Core\Persistence\Content\FieldValue;
use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition;
use Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldDefinition;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldValue;

class ImageAssetConverter implements Converter
{
    public function toStorageValue(FieldValue $value, StorageFieldValue $storageFieldValue)
    {
        $storageFieldValue->dataText = null !== $value->data ? json_encode($value->data) : null;
        $storageFieldValue->sortKeyInt = (int)$value->sortKey;
    }

    public function toFieldValue(StorageFieldValue $value, FieldValue $fieldValue)
    {
        $fieldValue->sortKey = (int)$value->sortKeyInt;

        if ($value->dataText === null) {
            $fieldValue->data = [
                'destinationContentId' => $value->dataInt ?: null,
                'alternativeText' => null,
                'source' => null,
            ];

            return;
        }

        $dataText = json_decode($value->dataText, true);
        if ($dataText === null) {
            $fieldValue->data = [
                'destinationContentId' => $value->dataInt ?: null,
                'alternativeText' => $value->dataText,
                'source' => null,
            ];

            return;
        }

        $fieldValue->data = $dataText;
    }

    public function toStorageFieldDefinition(FieldDefinition $fieldDef, StorageFieldDefinition $storageDef)
    {
    }

    public function toFieldDefinition(StorageFieldDefinition $storageDef, FieldDefinition $fieldDef)
    {
    }

    public function getIndexColumn()
    {
        return 'sort_key_string';
    }
}

class_alias(ImageAssetConverter::class, 'Ibexa\Platform\Connector\Dam\Persistance\FieldValue\ImageAssetConverter');
