<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement;

/**
 * @internal
 */
final class MeasurementValueFormat
{
    public const RANGE_VALUE_SEPARATOR = '...';

    public const RANGE_VALUE_FORMAT = '%f...%f%s';
    public const SIMPLE_VALUE_FORMAT = '%f%s';

    private function __construct()
    {
        // This class is not supposed to be instantiated.
    }
}
