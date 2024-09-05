<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Data;

use Ibexa\Personalization\Value\GranularityDateTimeRange;

class DateTimeRangeData
{
    /** @var \Ibexa\Personalization\Value\GranularityDateTimeRange */
    private $period;

    public function getPeriod(): ?GranularityDateTimeRange
    {
        return $this->period;
    }

    public function setPeriod(GranularityDateTimeRange $period): self
    {
        $this->period = $period;

        return $this;
    }
}

class_alias(DateTimeRangeData::class, 'Ibexa\Platform\Personalization\Form\Data\DateTimeRangeData');
