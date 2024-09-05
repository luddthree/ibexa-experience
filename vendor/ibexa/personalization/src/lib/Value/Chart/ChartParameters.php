<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Chart;

use Ibexa\Personalization\Value\GranularityDateTimeRange;

final class ChartParameters
{
    /** @var \Ibexa\Personalization\Value\GranularityDateTimeRange */
    private $dateTimeRange;

    public function __construct(GranularityDateTimeRange $dateTimeRange)
    {
        $this->dateTimeRange = $dateTimeRange;
    }

    public function getDateTimeRange(): GranularityDateTimeRange
    {
        return $this->dateTimeRange;
    }
}

class_alias(ChartParameters::class, 'Ibexa\Platform\Personalization\Value\Chart\ChartParameters');
