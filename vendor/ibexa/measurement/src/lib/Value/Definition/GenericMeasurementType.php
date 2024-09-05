<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\Value\Definition;

use Ibexa\Contracts\Measurement\Value\Definition\MeasurementInterface;
use Ibexa\Contracts\Measurement\Value\Definition\UnitInterface;
use Ibexa\Core\Base\Exceptions\NotFoundException;

/**
 * @internal
 */
class GenericMeasurementType implements MeasurementInterface
{
    private string $name;

    /** @var array<string, \Ibexa\Contracts\Measurement\Value\Definition\UnitInterface> */
    private array $unitList;

    private UnitInterface $baseUnit;

    /**
     * @param array<string, \Ibexa\Contracts\Measurement\Value\Definition\UnitInterface> $unitList
     */
    public function __construct(string $name, array $unitList, UnitInterface $baseUnit)
    {
        $this->name = $name;
        $this->unitList = $unitList;
        $this->baseUnit = $baseUnit;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function equals(MeasurementInterface $measurement): bool
    {
        return $this->name === $measurement->getName();
    }

    public function getUnitList(): iterable
    {
        return $this->unitList;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function hasUnit(string $unitName): bool
    {
        return array_key_exists($unitName, $this->unitList);
    }

    public function getUnit(string $unitName): UnitInterface
    {
        return $this->unitList[$unitName];
    }

    public function getBaseUnit(): UnitInterface
    {
        return $this->baseUnit;
    }

    public function getUnitBySymbol(string $symbol): UnitInterface
    {
        foreach ($this->unitList as $unit) {
            if ($unit->getSymbol() === $symbol) {
                return $unit;
            }
        }

        throw new NotFoundException(UnitInterface::class, $symbol);
    }
}
