<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Measurement\Value;

interface RangeValueInterface extends ValueInterface
{
    public function getMinValue(): float;

    public function getMaxValue(): float;

    public function equals(?RangeValueInterface $value): bool;
}
