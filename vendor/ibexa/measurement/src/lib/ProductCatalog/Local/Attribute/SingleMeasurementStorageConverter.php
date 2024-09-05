<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\ProductCatalog\Local\Attribute;

use Ibexa\Contracts\Measurement\MeasurementServiceInterface;
use Ibexa\Contracts\Measurement\Value\SimpleValueInterface;
use Ibexa\Contracts\ProductCatalog\Local\Attribute\StorageConverterInterface;
use Ibexa\Measurement\Storage\Gateway\Definition\TypeGatewayInterface;
use Ibexa\Measurement\Storage\Gateway\Definition\UnitGatewayInterface;
use Ibexa\Measurement\UnitConverter\UnitConverterDispatcherInterface;
use Webmozart\Assert\Assert;

/**
 * @implements \Ibexa\Contracts\ProductCatalog\Local\Attribute\StorageConverterInterface<
 *     array{
 *         unit_id: int|null,
 *         value: float|null,
 *         base_unit_id: int|null,
 *         base_value: float|null,
 *     },
 *     \Ibexa\Contracts\Measurement\Value\SimpleValueInterface,
 * >
 */
final class SingleMeasurementStorageConverter implements StorageConverterInterface
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

    public function fromPersistence(array $data): ?SimpleValueInterface
    {
        Assert::keyExists($data, SingleMeasurementStorageDefinition::COLUMN_VALUE);
        Assert::keyExists($data, SingleMeasurementStorageDefinition::COLUMN_UNIT_ID);

        if ($data[SingleMeasurementStorageDefinition::COLUMN_UNIT_ID] === null) {
            return null;
        }

        return $this->buildValue(
            $data[SingleMeasurementStorageDefinition::COLUMN_UNIT_ID],
            (float)$data[SingleMeasurementStorageDefinition::COLUMN_VALUE],
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
                SingleMeasurementStorageDefinition::COLUMN_VALUE => null,
                SingleMeasurementStorageDefinition::COLUMN_UNIT_ID => null,
                SingleMeasurementStorageDefinition::COLUMN_BASE_VALUE => null,
                SingleMeasurementStorageDefinition::COLUMN_BASE_UNIT_ID => null,
            ];
        }

        /** @var \Ibexa\Contracts\Measurement\Value\SimpleValueInterface $value */
        Assert::isInstanceOf($value, SimpleValueInterface::class);

        $measurement = $value->getMeasurement();
        $unit = $value->getUnit();

        $unitId = $this->unitGateway->getUnitId(
            $measurement->getName(),
            $unit->getIdentifier(),
        );

        if ($unit->isBaseUnit()) {
            return [
                SingleMeasurementStorageDefinition::COLUMN_VALUE => $value->getValue(),
                SingleMeasurementStorageDefinition::COLUMN_UNIT_ID => $unitId,
                SingleMeasurementStorageDefinition::COLUMN_BASE_VALUE => $value->getValue(),
                SingleMeasurementStorageDefinition::COLUMN_BASE_UNIT_ID => $unitId,
            ];
        }

        $baseValue = $this->unitConverter->convert($value, $measurement->getBaseUnit());
        $baseUnitId = $this->unitGateway->getUnitId(
            $measurement->getName(),
            $baseValue->getUnit()->getIdentifier(),
        );

        return [
            SingleMeasurementStorageDefinition::COLUMN_VALUE => $value->getValue(),
            SingleMeasurementStorageDefinition::COLUMN_UNIT_ID => $unitId,
            SingleMeasurementStorageDefinition::COLUMN_BASE_VALUE => $baseValue->getValue(),
            SingleMeasurementStorageDefinition::COLUMN_BASE_UNIT_ID => $baseUnitId,
        ];
    }

    /**
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     */
    private function buildValue(int $unitId, float $value): SimpleValueInterface
    {
        $typeName = $this->typeGateway->getTypeNameByUnitId($unitId);
        $unitIdentifier = $this->unitGateway->getUnitIdentifier($unitId);

        return $this->measurementService->buildSimpleValue(
            $typeName,
            $value,
            $unitIdentifier,
        );
    }
}
