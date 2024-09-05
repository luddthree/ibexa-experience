<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value;

final class TimePeriod
{
    public const LAST_24_HOURS = 'P0Y0M0DT24H';
    public const LAST_7_DAYS = 'P0Y0M7DT0H';
    public const LAST_30_DAYS = 'P0Y0M30DT0H';
    public const LAST_MONTH = 'P0Y1M0DT0H';
}

class_alias(TimePeriod::class, 'Ibexa\Platform\Personalization\Value\TimePeriod');
