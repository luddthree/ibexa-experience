<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Performance;

use Ibexa\Personalization\Value\Performance\Summary\ConversionRateList;
use Ibexa\Personalization\Value\Performance\Summary\EventList;
use Ibexa\Personalization\Value\Performance\Summary\RecommendationCallList;
use Ibexa\Personalization\Value\Performance\Summary\Revenue;

final class RecommendationEventsSummary
{
    private RecommendationCallList $recoCallListSummary;

    private ConversionRateList $conversionRateList;

    private EventList $eventList;

    private ?Revenue $revenueSummary;

    public function __construct(
        RecommendationCallList $recoCallListSummary,
        ConversionRateList $conversionRateList,
        EventList $eventList,
        ?Revenue $revenueSummary = null
    ) {
        $this->recoCallListSummary = $recoCallListSummary;
        $this->conversionRateList = $conversionRateList;
        $this->eventList = $eventList;
        $this->revenueSummary = $revenueSummary;
    }

    public function getRecommendationCallListSummary(): RecommendationCallList
    {
        return $this->recoCallListSummary;
    }

    public function getConversionSummary(): ConversionRateList
    {
        return $this->conversionRateList;
    }

    public function getEventList(): EventList
    {
        return $this->eventList;
    }

    public function getRevenueSummary(): ?Revenue
    {
        return $this->revenueSummary;
    }
}

class_alias(RecommendationEventsSummary::class, 'Ibexa\Platform\Personalization\Value\Performance\RecommendationEventsSummary');
