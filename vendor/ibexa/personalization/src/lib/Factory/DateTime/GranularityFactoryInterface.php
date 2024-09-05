<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Factory\DateTime;

use DateTime;
use Ibexa\Personalization\Value\GranularityDateTimeRange;

interface GranularityFactoryInterface
{
    public function createFromInterval(string $interval): GranularityDateTimeRange;

    public function createFromEndDateTimestampAndInterval(
        string $interval,
        int $endDateTimestamp
    ): GranularityDateTimeRange;

    public function createStartDateTime(string $interval, ?int $timestamp = null): DateTime;

    public function createEndDateTime(string $interval, ?int $timestamp = null): DateTime;

    public function getGranularity(string $interval): string;
}

class_alias(GranularityFactoryInterface::class, 'Ibexa\Platform\Personalization\Factory\DateTime\GranularityFactoryInterface');
