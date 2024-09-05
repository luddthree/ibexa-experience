<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value;

use DateTimeInterface;

final class DateTimeRange
{
    /** @var \DateTimeInterface */
    private $fromDate;

    /** @var \DateTimeInterface */
    private $toDate;

    public function __construct(
        DateTimeInterface $fromDate,
        DateTimeInterface $toDate
    ) {
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
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

class_alias(DateTimeRange::class, 'Ibexa\Platform\Personalization\Value\DateTimeRange');
