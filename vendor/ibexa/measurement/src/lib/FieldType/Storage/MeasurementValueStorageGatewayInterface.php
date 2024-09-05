<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\FieldType\Storage;

interface MeasurementValueStorageGatewayInterface
{
    public function storeSimpleValue(
        int $fieldId,
        int $versionNo,
        string $measurementType,
        string $measurementUnit,
        float $value
    ): void;

    public function storeRangeValue(
        int $fieldId,
        int $versionNo,
        string $measurementType,
        string $measurementUnit,
        float $minValue,
        float $maxValue
    ): void;

    /**
     * @return array{type_name: string, unit_identifier: string, value: float}
     */
    public function loadSimpleValueFieldData(int $fieldId, int $versionNo): array;

    /**
     * @return array{type_name: string, unit_identifier: string, min_value: float, max_value: float}
     */
    public function loadRangeValueFieldData(int $fieldId, int $versionNo): array;

    /**
     * @param int[] $fieldIds
     */
    public function deleteFieldData(array $fieldIds, int $versionNo): void;
}
