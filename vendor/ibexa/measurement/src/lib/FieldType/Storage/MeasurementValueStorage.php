<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\FieldType\Storage;

use Ibexa\Contracts\Core\FieldType\FieldStorage;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\VersionInfo;
use Ibexa\Measurement\FieldType\MeasurementType;
use LogicException;

/**
 * @internal
 */
final class MeasurementValueStorage implements FieldStorage
{
    private MeasurementValueStorageGatewayInterface $gateway;

    public function __construct(MeasurementValueStorageGatewayInterface $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @param array<string,mixed> $context
     */
    public function storeFieldData(VersionInfo $versionInfo, Field $field, array $context): ?bool
    {
        $data = $field->value->data;

        if ($data === null) {
            return null;
        }

        if (!is_array($data)) {
            throw new LogicException(sprintf(
                'Unable to store measurement value for field type "%s" (Field definition ID: %d). Data is not an array.',
                $field->type,
                $field->fieldDefinitionId,
            ));
        }

        if (isset($data['value'])) {
            $this->gateway->storeSimpleValue(
                $field->id,
                $versionInfo->versionNo,
                $data['measurementType'],
                $data['measurementUnit'],
                $data['value']
            );

            return null;
        }

        if (isset($data['measurementRangeMinimumValue'], $data['measurementRangeMaximumValue'])) {
            $this->gateway->storeRangeValue(
                $field->id,
                $versionInfo->versionNo,
                $data['measurementType'],
                $data['measurementUnit'],
                $data['measurementRangeMinimumValue'],
                $data['measurementRangeMaximumValue']
            );

            return null;
        }

        throw new LogicException(sprintf(
            'Unable to store measurement value for field type "%s" (Field definition ID: %d).'
            . ' Either single "value" or both "measurementRangeMinimumValue" and "measurementRangeMaximumValue"'
            . ' should be provided as data.',
            $field->type,
            $field->fieldDefinitionId,
        ));
    }

    /**
     * @param array<string,mixed> $context
     */
    public function getFieldData(VersionInfo $versionInfo, Field $field, array $context): void
    {
        $inputType = $field->value->data['inputType'] ?? null;

        $data = null;
        if ($inputType === MeasurementType::INPUT_TYPE_SIMPLE_INPUT) {
            $data = $this->gateway->loadSimpleValueFieldData($field->id, $versionInfo->versionNo);
        } elseif ($inputType === MeasurementType::INPUT_TYPE_RANGE) {
            $data = $this->gateway->loadRangeValueFieldData($field->id, $versionInfo->versionNo);
        }

        $field->value->data = !empty($data) ? $this->mapRowsToFieldData($inputType, $data) : null;
    }

    /**
     * @param array<string,mixed> $context
     * @param int[] $fieldIds
     */
    public function deleteFieldData(VersionInfo $versionInfo, array $fieldIds, array $context): bool
    {
        $this->gateway->deleteFieldData($fieldIds, $versionInfo->versionNo);

        return true;
    }

    public function hasFieldData(): bool
    {
        return true;
    }

    /**
     * @param array<string,mixed> $context
     *
     * @return \Ibexa\Contracts\Core\Search\Field[]|null
     */
    public function getIndexData(VersionInfo $versionInfo, Field $field, array $context): ?array
    {
        return null;
    }

    /**
     * @param array{type_name: string, unit_identifier: string, value?: float, min_value?:float, max_value?:float} $data
     *
     * @return array{
     *      inputType: int,
     *      measurementType: string,
     *      measurementUnit: string,
     *      value?: float,
     *      measurementRangeMinimumValue?: float,
     *      measurementRangeMaximumValue?: float
     * }
     */
    private function mapRowsToFieldData(int $inputType, array $data): array
    {
        $fieldData = [
            'inputType' => $inputType,
            'measurementType' => $data['type_name'],
            'measurementUnit' => $data['unit_identifier'],
        ];
        if (isset($data['value'])) {
            $fieldData['value'] = (float)$data['value'];
        }

        if (isset($data['min_value'], $data['max_value'])) {
            $fieldData['measurementRangeMinimumValue'] = (float)$data['min_value'];
            $fieldData['measurementRangeMaximumValue'] = (float)$data['max_value'];
        }

        return $fieldData;
    }

    /**
     * @param array<string,mixed> $context
     */
    public function copyLegacyField(
        VersionInfo $versionInfo,
        Field $field,
        Field $originalField,
        array $context
    ): ?bool {
        return $this->storeFieldData($versionInfo, $field, $context);
    }
}
