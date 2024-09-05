<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Data;

final class DashboardData
{
    private DateTimeRangeData $chart;

    private PopularityDurationChoiceData $popularity;

    private ?DateTimeRangeData $revenue;

    public function getChart(): DateTimeRangeData
    {
        return $this->chart;
    }

    public function setChart(DateTimeRangeData $chart): self
    {
        $this->chart = $chart;

        return $this;
    }

    public function getPopularity(): PopularityDurationChoiceData
    {
        return $this->popularity;
    }

    public function setPopularity(PopularityDurationChoiceData $popularity): self
    {
        $this->popularity = $popularity;

        return $this;
    }

    public function getRevenue(): ?DateTimeRangeData
    {
        return $this->revenue;
    }

    public function setRevenue(?DateTimeRangeData $revenue = null): self
    {
        $this->revenue = $revenue;

        return $this;
    }
}

class_alias(DashboardData::class, 'Ibexa\Platform\Personalization\Form\Data\DashboardData');
