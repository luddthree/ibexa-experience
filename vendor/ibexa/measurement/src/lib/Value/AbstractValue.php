<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\Value;

use Ibexa\Contracts\Measurement\Value\Definition\MeasurementInterface;
use Ibexa\Contracts\Measurement\Value\Definition\UnitInterface;
use Ibexa\Contracts\Measurement\Value\ValueInterface;

abstract class AbstractValue implements ValueInterface
{
    protected MeasurementInterface $measurement;

    protected UnitInterface $unit;

    final public function getMeasurement(): MeasurementInterface
    {
        return $this->measurement;
    }

    final public function getUnit(): UnitInterface
    {
        return $this->unit;
    }
}
