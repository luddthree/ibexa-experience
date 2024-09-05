<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Measurement\Value\Definition;

use Stringable;

interface MeasurementInterface extends Stringable
{
    public function getName(): string;

    /**
     * @return iterable<string, \Ibexa\Contracts\Measurement\Value\Definition\UnitInterface>
     */
    public function getUnitList(): iterable;

    public function hasUnit(string $unitName): bool;

    public function getUnit(string $unitName): UnitInterface;

    public function getBaseUnit(): UnitInterface;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function getUnitBySymbol(string $symbol): UnitInterface;

    public function equals(MeasurementInterface $measurement): bool;
}
