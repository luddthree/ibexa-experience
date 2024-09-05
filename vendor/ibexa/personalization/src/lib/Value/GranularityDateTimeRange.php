<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value;

use DateTimeInterface;

final class GranularityDateTimeRange
{
    /** @var string */
    private $granularity;

    /** @var \DateTimeInterface */
    private $fromDate;

    /** @var \DateTimeInterface */
    private $toDate;

    public function __construct(
        string $granularity,
        DateTimeInterface $fromDate,
        DateTimeInterface $toDate
    ) {
        $this->granularity = $granularity;
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
    }

    public function getGranularity(): string
    {
        return $this->granularity;
    }

    public function getFromDate(): DateTimeInterface
    {
        return $this->fromDate;
    }

    public function getToDate(): DateTimeInterface
    {
        return $this->toDate;
    }
}

class_alias(GranularityDateTimeRange::class, 'Ibexa\Platform\Personalization\Value\GranularityDateTimeRange');
