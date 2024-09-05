<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\FieldType;

use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\Core\Exception\InvalidArgumentType;
use Ibexa\Contracts\Core\FieldType\Value as BaseValue;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Contracts\Measurement\ConfigResolver\MeasurementTypesInterface;
use Ibexa\Contracts\Measurement\MeasurementServiceInterface;
use Ibexa\Contracts\Measurement\Value\RangeValueInterface;
use Ibexa\Contracts\Measurement\Value\SimpleValueInterface;
use Ibexa\Contracts\Measurement\Value\ValueInterface;
use Ibexa\Core\FieldType\FieldType;
use Ibexa\Core\FieldType\ValidationError;

/**
 * @internal
 */
final class MeasurementType extends FieldType
{
    public const IDENTIFIER = 'ibexa_measurement';

    public const INPUT_TYPE_SIMPLE_INPUT = 0;

    public const INPUT_TYPE_RANGE = 1;

    private MeasurementTypesInterface $measurementTypes;

    private MeasurementServiceInterface $measurementService;

    public function __construct(
        MeasurementTypesInterface $measurementTypes,
        MeasurementServiceInterface $measurementService
    ) {
        $this->measurementTypes = $measurementTypes;
        $this->measurementService = $measurementService;
    }

    protected $validatorConfigurationSchema = [
        'MeasurementValidator' => [
            'measurementType' => [
                'type' => 'string',
                'default' => null,
            ],
            'measurementUnit' => [
                'type' => 'string',
                'default' => null,
            ],
            'inputType' => [
                'type' => 'int',
                'default' => null,
            ],
            'sign' => [
                'type' => 'string',
                'default' => null,
            ],
            'minimum' => [
                'type' => 'float',
                'default' => null,
            ],
            'maximum' => [
                'type' => 'float',
                'default' => null,
            ],
            'defaultValue' => [
                'type' => 'float',
                'default' => null,
            ],
            'defaultRangeMinimumValue' => [
                'type' => 'float',
                'default' => null,
            ],
            'defaultRangeMaximumValue' => [
                'type' => 'float',
                'default' => null,
            ],
        ],
    ];

    public function getFieldTypeIdentifier(): string
    {
        return self::IDENTIFIER;
    }

    public function getName(
        BaseValue $value,
        FieldDefinition $fieldDefinition,
        string $languageCode
    ): string {
        return (string)$value;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     */
    protected function createValueFromInput($inputValue): MeasurementValue
    {
        if (!$inputValue instanceof MeasurementValue) {
            throw new InvalidArgumentException(
                '$inputValue',
                sprintf(
                    'Input value should be an instance of "%s", "%s" given instead',
                    MeasurementValue::class,
                    is_object($inputValue) ? get_class($inputValue) : gettype($inputValue)
                )
            );
        }

        return $inputValue;
    }

    public function getEmptyValue(): MeasurementValue
    {
        return new MeasurementValue();
    }

    /**
     * @param ?array{
     *     'measurementType': string,
     *     'measurementUnit': string,
     *     'value': float,
     *     'inputType': int,
     *     'measurementRangeMinimumValue': float,
     *     'measurementRangeMaximumValue': float,
     * } $hash
     *
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     */
    public function fromHash($hash): MeasurementValue
    {
        if ($hash === null || !array_key_exists('inputType', $hash)) {
            return $this->getEmptyValue();
        }

        if ((int)$hash['inputType'] === self::INPUT_TYPE_SIMPLE_INPUT) {
            $value = $this->measurementService->buildSimpleValue(
                $hash['measurementType'],
                (float)$hash['value'],
                $hash['measurementUnit']
            );
        } else {
            $value = $this->measurementService->buildRangeValue(
                $hash['measurementType'],
                (float)$hash['measurementRangeMinimumValue'],
                (float)$hash['measurementRangeMaximumValue'],
                $hash['measurementUnit']
            );
        }

        return new MeasurementValue($value);
    }

    /**
     * @param \Ibexa\Measurement\FieldType\MeasurementValue $value
     *
     * @return ?array{
     *     measurementType: string,
     *     measurementUnit: string,
     *     inputType: int,
     *     value: float,
     *     measurementRangeMinimumValue: float,
     *     measurementRangeMaximumValue: float
     * }
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function toHash(BaseValue $value): ?array
    {
        if ($this->isEmptyValue($value)) {
            return null;
        }

        $this->checkValueStructure($value);

        $nativeValue = $value->getValue();
        if ($nativeValue === null) {
            return null;
        }

        return $this->buildHashFromNativeValue($nativeValue);
    }

    protected static function checkValueType($value): void
    {
        if (!$value instanceof MeasurementValue) {
            throw new InvalidArgumentType(
                '$value',
                MeasurementValue::class,
                $value
            );
        }
    }

    protected function checkValueStructure(BaseValue $value): void
    {
        self::checkValueType($value);
    }

    public function validate(FieldDefinition $fieldDefinition, BaseValue $fieldValue)
    {
        $errors = [];

        if ($this->isEmptyValue($fieldValue)) {
            return $errors;
        }

        return $errors;
    }

    /**
     * Validates the validatorConfiguration of a FieldDefinitionCreateStruct or FieldDefinitionUpdateStruct.
     *
     * @param mixed $validatorConfiguration
     *
     * @return \Ibexa\Contracts\Core\FieldType\ValidationError[]
     */
    public function validateValidatorConfiguration($validatorConfiguration): array
    {
        $validationErrors = [];
        foreach ((array)$validatorConfiguration as $validatorIdentifier => $parameters) {
            /** @var array<string,mixed> $parameters */
            if ($validatorIdentifier === 'MeasurementValidator') {
                $validationErrors = $this->validateMeasurement(
                    $parameters,
                    $validatorIdentifier,
                    $validationErrors
                );
            } else {
                $validationErrors[] = new ValidationError(
                    "Validator '%validator%' is unknown",
                    null,
                    [
                        '%validator%' => $validatorIdentifier,
                    ],
                    "[$validatorIdentifier]"
                );
            }
        }

        return $validationErrors;
    }

    /**
     * @param array<string,mixed> $parameters
     * @param \Ibexa\Core\FieldType\ValidationError[] $validationErrors
     *
     * @return \Ibexa\Core\FieldType\ValidationError[]
     */
    private function validateMeasurement(
        array $parameters,
        string $validatorIdentifier,
        array $validationErrors
    ): array {
        $validationErrors = $this->validateParameterIsString(
            $parameters,
            'measurementType',
            $validatorIdentifier,
            $validationErrors
        );

        $validationErrors = $this->validateParameterIsString(
            $parameters,
            'measurementUnit',
            $validatorIdentifier,
            $validationErrors
        );

        if (isset($parameters['measurementType'])) {
            assert(is_string($parameters['measurementType']));
            $unitsInType = $this->measurementTypes->getUnitsByType(
                $parameters['measurementType']
            );

            if (!in_array($parameters['measurementUnit'], $unitsInType, true)) {
                $validationErrors[] = new ValidationError(
                    'Validator %validator% expects parameter %parameter% to be in type %$type%.',
                    null,
                    [
                        '%validator%' => $validatorIdentifier,
                        '%parameter%' => 'measurementUnit',
                        '%$type%' => $parameters['measurementType'],
                    ],
                    "[$validatorIdentifier][measurementUnit]"
                );
            }
        }

        return $validationErrors;
    }

    /**
     * @param array<string, mixed> $parameters
     * @param \Ibexa\Core\FieldType\ValidationError[] $validationErrors
     *
     * @return \Ibexa\Core\FieldType\ValidationError[]
     */
    private function validateParameterIsString(
        array $parameters,
        string $parameterName,
        string $validatorIdentifier,
        array $validationErrors
    ): array {
        if (!array_key_exists($parameterName, $parameters)) {
            $validationErrors[] = new ValidationError(
                'Validator %validator% expects parameter %parameter% to be set.',
                null,
                [
                    '%validator%' => $validatorIdentifier,
                    '%parameter%' => $parameterName,
                ],
                "[$validatorIdentifier]"
            );
        }

        $value = $parameters[$parameterName];
        if (!is_string($value) && $value !== null) {
            $validationErrors[] = new ValidationError(
                'Validator %validator% expects parameter %parameter% to be of %type%.',
                null,
                [
                    '%validator%' => $validatorIdentifier,
                    '%parameter%' => $parameterName,
                    '%type%' => 'string',
                ],
                "[$validatorIdentifier][$parameterName]"
            );
        }

        return $validationErrors;
    }

    /**
     * @return array{
     *     measurementType: string,
     *     measurementUnit: string,
     *     inputType: int,
     *     value?: float,
     *     measurementRangeMinimumValue?: float,
     *     measurementRangeMaximumValue?: float
     * }
     */
    private function buildHashFromNativeValue(ValueInterface $nativeValue): ?array
    {
        $hash = [
            'measurementType' => $nativeValue->getMeasurement()->getName(),
            'measurementUnit' => $nativeValue->getUnit()->getIdentifier(),
        ];

        if ($nativeValue instanceof SimpleValueInterface) {
            $hash['value'] = $nativeValue->getValue();
            $hash['inputType'] = self::INPUT_TYPE_SIMPLE_INPUT;
        } elseif ($nativeValue instanceof RangeValueInterface) {
            $hash['measurementRangeMinimumValue'] = $nativeValue->getMinValue();
            $hash['measurementRangeMaximumValue'] = $nativeValue->getMaxValue();
            $hash['inputType'] = self::INPUT_TYPE_RANGE;
        } else {
            return null;
        }

        return $hash;
    }
}
