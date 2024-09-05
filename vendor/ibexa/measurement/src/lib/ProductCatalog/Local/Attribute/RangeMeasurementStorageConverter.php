<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\ProductCatalog\Local\Attribute;

use Ibexa\Contracts\Measurement\MeasurementServiceInterface;
use Ibexa\Contracts\Measurement\Value\RangeValueInterface;
use Ibexa\Contracts\ProductCatalog\Local\Attribute\StorageConverterInterface;
use Ibexa\Measurement\Storage\Gateway\Definition\TypeGatewayInterface;
use Ibexa\Measurement\Storage\Gateway\Definition\UnitGatewayInterface;
use Ibexa\Measurement\UnitConverter\UnitConverterDispatcherInterface;
use Webmozart\Assert\Assert;

/**
 * @implements \Ibexa\Contracts\ProductCatalog\Local\Attribute\StorageConverterInterface<
 *     array{
 *         unit_id: int|null,
 *         min_value: float|null,
 *         max_value: float|null,
 *         base_unit_id: int|null,
 *         base_min_value: float|null,
 *         base_max_value: float|null,
 *     },
 *     \Ibexa\Contracts\Measurement\Value\RangeValueInterface,
 * >
 */
final class RangeMeasurementStorageConverter implements StorageConverterInterface
{
    private MeasurementServiceInterface $measurementService;

    private UnitGatewayInterface $unitGateway;

    private TypeGatewayInterface $typeGateway;

    private UnitConverterDispatcherInterface $unitConverter;

    public function __construct(
        MeasurementServiceInterface $measurementService,
        UnitGatewayInterface $unitGateway,
        TypeGatewayInterface $typeGateway,
        UnitConverterDispatcherInterface $unitConverter
    ) {
        $this->measurementService = $measurementService;
        $this->unitGateway = $unitGateway;
        $this->typeGateway = $typeGateway;
        $this->unitConverter = $unitConverter;
    }

    public function fromPersistence(array $data): ?RangeValueInterface
    {
        Assert::keyExists($data, RangeMeasurementStorageDefinition::COLUMN_MIN_VALUE);
        Assert::keyExists($data, RangeMeasurementStorageDefinition::COLUMN_MAX_VALUE);
        Assert::keyExists($data, RangeMeasurementStorageDefinition::COLUMN_UNIT_ID);
        Assert::nullOrInteger($data[RangeMeasurementStorageDefinition::COLUMN_UNIT_ID]);

        if ($data[RangeMeasurementStorageDefinition::COLUMN_UNIT_ID] === null) {
            return null;
        }

        Assert::float($data[RangeMeasurementStorageDefinition::COLUMN_MIN_VALUE]);
        Assert::float($data[RangeMeasurementStorageDefinition::COLUMN_MAX_VALUE]);

        return $this->buildValue(
            $data[RangeMeasurementStorageDefinition::COLUMN_UNIT_ID],
            $data[RangeMeasurementStorageDefinition::COLUMN_MIN_VALUE],
            $data[RangeMeasurementStorageDefinition::COLUMN_MAX_VALUE]
        );
    }

    /**
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     * @throws \Ibexa\Measurement\UnitConverter\Exception\UnitConversionException
     */
    public function toPersistence($value): array
    {
        if ($value === null) {
            return [
                RangeMeasurementStorageDefinition::COLUMN_MIN_VALUE => null,
                RangeMeasurementStorageDefinition::COLUMN_MAX_VALUE => null,
                RangeMeasurementStorageDefinition::COLUMN_UNIT_ID => null,
                RangeMeasurementStorageDefinition::COLUMN_BASE_MIN_VALUE => null,
                RangeMeasurementStorageDefinition::COLUMN_BASE_MAX_VALUE => null,
                RangeMeasurementStorageDefinition::COLUMN_BASE_UNIT_ID => null,
            ];
        }

        Assert::isInstanceOf($value, RangeValueInterface::class);

        $unit = $value->getUnit();
        $measurement = $value->getMeasurement();
        $unitId = $this->unitGateway->getUnitId(
            $measurement->getName(),
            $unit->getIdentifier(),
        );

        if ($unit->isBaseUnit()) {
            return [
                RangeMeasurementStorageDefinition::COLUMN_MIN_VALUE => $value->getMinValue(),
                RangeMeasurementStorageDefinition::COLUMN_MAX_VALUE => $value->getMaxValue(),
                RangeMeasurementStorageDefinition::COLUMN_UNIT_ID => $unitId,
                RangeMeasurementStorageDefinition::COLUMN_BASE_MIN_VALUE => $value->getMinValue(),
                RangeMeasurementStorageDefinition::COLUMN_BASE_MAX_VALUE => $value->getMaxValue(),
                RangeMeasurementStorageDefinition::COLUMN_BASE_UNIT_ID => $unitId,
            ];
        }

        $baseValue = $this->unitConverter->convert($value, $measurement->getBaseUnit());
        $baseUnit = $baseValue->getUnit();
        $baseUnitId = $this->unitGateway->getUnitId(
            $measurement->getName(),
            $baseUnit->getIdentifier(),
        );

        return [
            RangeMeasurementStorageDefinition::COLUMN_MIN_VALUE => $value->getMinValue(),
            RangeMeasurementStorageDefinition::COLUMN_MAX_VALUE => $value->getMaxValue(),
            RangeMeasurementStorageDefinition::COLUMN_UNIT_ID => $unitId,
            RangeMeasurementStorageDefinition::COLUMN_BASE_MIN_VALUE => $baseValue->getMinValue(),
            RangeMeasurementStorageDefinition::COLUMN_BASE_MAX_VALUE => $baseValue->getMaxValue(),
            RangeMeasurementStorageDefinition::COLUMN_BASE_UNIT_ID => $baseUnitId,
        ];
    }

    /**
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     */
    private function buildValue(int $unitId, float $minValue, float $maxValue): RangeValueInterface
    {
        $typeName = $this->typeGateway->getTypeNameByUnitId($unitId);
        $unitIdentifier = $this->unitGateway->getUnitIdentifier($unitId);

        return $this->measurementService->buildRangeValue(
            $typeName,
            $minValue,
            $maxValue,
            $unitIdentifier,
        );
    }
}
