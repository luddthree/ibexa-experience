<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\CDP\Export\Content\FieldValueProcessor\FieldType;

use Ibexa\Contracts\Cdp\Export\Content\FieldValueProcessorInterface;
use Ibexa\Contracts\Core\FieldType\FieldType;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Measurement\MeasurementServiceInterface;
use Ibexa\Contracts\Measurement\MeasurementTypeFactoryInterface;
use Ibexa\Contracts\Measurement\Value\ValueInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;

abstract class AbstractMeasurementFieldValueProcessor implements FieldValueProcessorInterface
{
    public const KEY_VALUE_MEASUREMENT = 'value_measurement';

    public const KEY_VALUE_BASE_UNIT_SYMBOL = 'value_base_unit_symbol';

    public const KEY_VALUE_BASE_UNIT_IDENTIFIER = 'value_base_unit_identifier';

    public const KEY_VALUE_UNIT_SYMBOL = 'value_unit_symbol';

    public const KEY_VALUE_UNIT_IDENTIFIER = 'value_unit_identifier';

    public const KEY_VALUE_UNIT_IS_BASE = 'value_unit_is_base';

    private FieldType $fieldType;

    private MeasurementServiceInterface $measurementService;

    private MeasurementTypeFactoryInterface $measurementTypeFactory;

    public function __construct(
        FieldType $fieldType,
        MeasurementServiceInterface $measurementService,
        MeasurementTypeFactoryInterface $measurementTypeFactory
    ) {
        $this->fieldType = $fieldType;
        $this->measurementService = $measurementService;
        $this->measurementTypeFactory = $measurementTypeFactory;
    }

    public function process(Field $field, Content $content): array
    {
        /** @var \Ibexa\Measurement\FieldType\MeasurementValue $value */
        $value = $field->getValue();
        /** @var \Ibexa\Contracts\Measurement\Value\ValueInterface $measurementValue */
        $measurementValue = $value->getValue();

        $contentType = $content->getContentType();
        $fieldDefinition = $contentType->getFieldDefinition($field->getFieldDefinitionIdentifier());

        if (null === $fieldDefinition) {
            throw new InvalidArgumentException(
                '$field',
                sprintf(
                    'Cannot fetch Field Definition %s from content type %s',
                    $field->getFieldDefinitionIdentifier(),
                    $contentType->identifier,
                ),
            );
        }
        $validatorConfiguration = $fieldDefinition->getValidatorConfiguration()['MeasurementValidator'] ?? [];

        $measurementType = $this->measurementTypeFactory->buildType($validatorConfiguration['measurementType']);
        $baseUnit = $measurementType->getBaseUnit();

        $values = $this->normalizeMeasurementValue(null);
        $valuesInBaseUnit = $this->appendKeys($values, 'base_unit');
        $baseData = [
            self::KEY_VALUE_MEASUREMENT => $measurementType->getName(),
            self::KEY_VALUE_UNIT_IDENTIFIER => null,
            self::KEY_VALUE_UNIT_SYMBOL => null,
            self::KEY_VALUE_UNIT_IS_BASE => null,
            self::KEY_VALUE_BASE_UNIT_IDENTIFIER => $baseUnit->getIdentifier(),
            self::KEY_VALUE_BASE_UNIT_SYMBOL => $baseUnit->getSymbol(),
        ];

        if (null !== $measurementValue) {
            $measurement = $measurementValue->getMeasurement();
            $unit = $measurementValue->getUnit();
            $measurementValueInBaseUnit = !$unit->isBaseUnit()
                ? $this->measurementService->convert(
                    $measurementValue,
                    $measurement->getBaseUnit(),
                )
                : $measurementValue;

            $values = $this->normalizeMeasurementValue($measurementValue);
            $valuesInBaseUnit = $this->appendKeys(
                $this->normalizeMeasurementValue($measurementValueInBaseUnit),
                'base_unit'
            );

            $baseData = array_replace(
                $baseData,
                [
                    self::KEY_VALUE_UNIT_SYMBOL => $unit->getSymbol(),
                    self::KEY_VALUE_UNIT_IDENTIFIER => $unit->getIdentifier(),
                    self::KEY_VALUE_UNIT_IS_BASE => $unit->isBaseUnit(),
                ],
            );
        }

        return array_merge(
            $baseData,
            $values,
            $valuesInBaseUnit,
        );
    }

    public function supports(Field $field, Content $content): bool
    {
        $contentType = $content->getContentType();
        $fieldDefinition = $contentType->getFieldDefinition($field->getFieldDefinitionIdentifier());

        if (null === $fieldDefinition) {
            return false;
        }

        $inputType = $fieldDefinition->getValidatorConfiguration()['MeasurementValidator']['inputType'] ?? null;
        $isCompatibleMeasurementType = $inputType === $this->getValueType();

        return $this->fieldType->getFieldTypeIdentifier() === $field->fieldTypeIdentifier
            && $isCompatibleMeasurementType;
    }

    /**
     * @return \Ibexa\Measurement\FieldType\MeasurementType::INPUT_TYPE_*
     */
    abstract protected function getValueType(): int;

    /**
     * @return array<string, scalar|null>
     */
    abstract protected function normalizeMeasurementValue(?ValueInterface $measurementValue): array;

    /**
     * @param array<string, scalar|null> $measurementData
     *
     * @return array<string, scalar|null>
     */
    private function appendKeys(array $measurementData, string $suffix): array
    {
        return array_combine(
            array_map(
                static fn ($key): string => $key . '_' . $suffix,
                array_keys($measurementData),
            ),
            $measurementData,
        );
    }
}
