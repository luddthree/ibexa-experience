<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\Performance;

use Ibexa\Personalization\Value\GranularityDateTimeRange;
use Psr\Http\Message\ResponseInterface;

interface EventsDetailsDataFetcherInterface
{
    public function fetchRecommendationEventsDetails(
        int $customerId,
        string $licenseKey,
        GranularityDateTimeRange $granularityDateTimeRange
    ): ResponseInterface;
}

class_alias(EventsDetailsDataFetcherInterface::class, 'Ibexa\Platform\Personalization\Client\Consumer\Performance\EventsDetailsDataFetcherInterface');
