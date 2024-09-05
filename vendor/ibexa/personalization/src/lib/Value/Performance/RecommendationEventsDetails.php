<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Performance;

use Ibexa\Personalization\Value\Performance\Details\EventList;
use Ibexa\Personalization\Value\Performance\Details\RevenueList;

final class RecommendationEventsDetails
{
    private RevenueList $revenueList;

    private EventList $eventList;

    public function __construct(
        RevenueList $revenueList,
        EventList $eventList
    ) {
        $this->revenueList = $revenueList;
        $this->eventList = $eventList;
    }

    public function getRevenueList(): RevenueList
    {
        return $this->revenueList;
    }

    public function getEventList(): EventList
    {
        return $this->eventList;
    }
}

class_alias(RecommendationEventsDetails::class, 'Ibexa\Platform\Personalization\Value\Performance\RecommendationEventsDetails');
