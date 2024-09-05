<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\ValueFactory;

use Ibexa\Contracts\Measurement\Value\RangeValueInterface;
use Ibexa\Contracts\Measurement\Value\SimpleValueInterface;

interface ValueFactoryInterface
{
    /**
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     */
    public function createSimpleValue(
        string $typeName,
        float $value,
        string $unitName
    ): SimpleValueInterface;

    /**
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     */
    public function createRangeValue(
        string $typeName,
        float $minValue,
        float $maxValue,
        string $unitName
    ): RangeValueInterface;
}
