<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\FieldType;

use Ibexa\Contracts\Core\FieldType\Indexable;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition;
use Ibexa\Contracts\Core\Search;

/**
 * @internal
 */
final class MeasurementSearchField implements Indexable
{
    /**
     * @var array<MeasurementType::INPUT_TYPE_*, 'simple'|'range'>
     **/
    private const INPUT_TYPES = [
        MeasurementType::INPUT_TYPE_SIMPLE_INPUT => 'simple',
        MeasurementType::INPUT_TYPE_RANGE => 'range',
    ];

    public function getIndexData(Field $field, FieldDefinition $fieldDefinition): array
    {
        $data = $field->value->data;
        /** @var ?array{
         *     inputType?: integer,
         *     measurementType: string,
         *     measurementUnit: string,
         *     value?: float,
         *     measurementRangeMinimumValue?: float,
         *     measurementRangeMaximumValue?: float
         * } $data
         */
        if (null === $data || !isset($data['inputType'])) {
            return [];
        }

        $inputType = $data['inputType'];

        return $inputType === MeasurementType::INPUT_TYPE_SIMPLE_INPUT
            ? $this->getIndexDataForSimpleValue($data)
            : $this->getIndexDataForRangeValue($data);
    }

    public function getIndexDefinition(): array
    {
        return [
            'type' => new Search\FieldType\StringField(),
            'unit' => new Search\FieldType\StringField(),
            'value_type' => new Search\FieldType\StringField(),
            'value' => new Search\FieldType\FloatField(),
            'min_value' => new Search\FieldType\FloatField(),
            'max_value' => new Search\FieldType\FloatField(),
        ];
    }

    public function getDefaultMatchField(): ?string
    {
        return 'value';
    }

    public function getDefaultSortField(): ?string
    {
        return $this->getDefaultMatchField();
    }

    /**
     * @param array{inputType: integer, measurementType: string, measurementUnit: string, value?: float} $data
     *
     * @return array<\Ibexa\Contracts\Core\Search\Field>
     */
    private function getIndexDataForSimpleValue(array $data): array
    {
        return array_merge(
            $this->getCommonIndexData($data),
            [
                new Search\Field(
                    'value',
                    $data['value'] ?? null,
                    new Search\FieldType\FloatField()
                ),
                new Search\Field(
                    'fulltext',
                    sprintf(
                        '%s: %f %s',
                        $data['measurementType'],
                        $data['value'] ?? '',
                        $data['measurementUnit']
                    ),
                    new Search\FieldType\FullTextField()
                ),
            ]
        );
    }

    /**
     * @param array{inputType: integer, measurementType: string, measurementUnit: string, measurementRangeMinimumValue?: float, measurementRangeMaximumValue?: float} $data
     *
     * @return array<\Ibexa\Contracts\Core\Search\Field>
     */
    private function getIndexDataForRangeValue(array $data): array
    {
        return array_merge(
            $this->getCommonIndexData($data),
            [
                new Search\Field(
                    'min_value',
                    $data['measurementRangeMinimumValue'] ?? null,
                    new Search\FieldType\FloatField()
                ),
                new Search\Field(
                    'max_value',
                    $data['measurementRangeMaximumValue'] ?? null,
                    new Search\FieldType\FloatField()
                ),
                new Search\Field(
                    'fulltext',
                    sprintf(
                        '%s: %f - %f %s',
                        $data['measurementType'],
                        $data['measurementRangeMinimumValue'] ?? '',
                        $data['measurementRangeMaximumValue'] ?? '',
                        $data['measurementUnit']
                    ),
                    new Search\FieldType\FullTextField()
                ),
            ]
        );
    }

    /**
     * @param array{inputType: integer, measurementType: string, measurementUnit: string} $data
     *
     * @return array<\Ibexa\Contracts\Core\Search\Field>
     */
    private function getCommonIndexData(array $data): array
    {
        return [
            new Search\Field(
                'type',
                $data['measurementType'],
                new Search\FieldType\StringField()
            ),
            new Search\Field(
                'unit',
                $data['measurementUnit'],
                new Search\FieldType\StringField()
            ),
            new Search\Field(
                'value_type',
                self::INPUT_TYPES[$data['inputType']],
                new Search\FieldType\StringField()
            ),
        ];
    }
}
