<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement;

use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\Measurement\MeasurementTypeFactoryInterface;
use Ibexa\Contracts\Measurement\Value\Definition\MeasurementInterface;
use Ibexa\Measurement\Exception\MeasurementTypeConfigurationException;
use Ibexa\Measurement\Value\Definition\GenericMeasurementType;
use Ibexa\Measurement\Value\Definition\GenericUnit;

/**
 * @internal
 *
 * @phpstan-type MeasurementTypesExtensionConfig array<
 *     string,
 *     array<
 *         string,
 *         array{
 *             symbol: string,
 *             is_base_unit: boolean,
 *         },
 *     >,
 * >
 */
final class MeasurementTypeFactory implements MeasurementTypeFactoryInterface
{
    /** @phpstan-var MeasurementTypesExtensionConfig */
    private array $types;

    /**
     * @phpstan-param MeasurementTypesExtensionConfig $types
     */
    public function __construct(array $types)
    {
        $this->types = $types;
    }

    public function buildType(string $typeName): MeasurementInterface
    {
        $typeConfiguration = $this->types[$typeName] ?? null;
        if (null === $typeConfiguration) {
            throw new InvalidArgumentException(
                '$typeName',
                "Measurement Type $typeName not found"
            );
        }

        $baseUnit = null;

        $unitList = [];
        foreach ($typeConfiguration as $unitName => $unitConfiguration) {
            $isBaseUnit = $unitConfiguration['is_base_unit'];

            $unit = new GenericUnit(
                $unitName,
                $unitConfiguration['symbol'],
                $isBaseUnit
            );

            if ($isBaseUnit) {
                $baseUnit = $unit;
            }

            $unitList[$unitName] = $unit;
        }

        if ($baseUnit === null) {
            throw new MeasurementTypeConfigurationException(sprintf('Missing base unit for type: %s', $typeName));
        }

        return new GenericMeasurementType($typeName, $unitList, $baseUnit);
    }

    public function hasType(string $typeName): bool
    {
        return array_key_exists($typeName, $this->types);
    }

    public function getAvailableTypes(): array
    {
        return array_keys($this->types);
    }
}
