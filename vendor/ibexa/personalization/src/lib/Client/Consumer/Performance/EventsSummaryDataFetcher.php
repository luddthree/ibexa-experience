<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\Performance;

use Ibexa\Personalization\Client\Consumer\AbstractPersonalizationConsumer;
use Ibexa\Personalization\Client\PersonalizationClientInterface;
use Ibexa\Personalization\Value\DateTimeRange;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;

final class EventsSummaryDataFetcher extends AbstractPersonalizationConsumer implements EventsSummaryDataFetcherInterface
{
    private const ENDPOINT_URI = '/api/v5/%d/recommendation/performance/events-summary';
    private const DURATION_DAILY = 'H24';
    private const DURATION_WEEKLY = 'WEEK';

    public function __construct(PersonalizationClientInterface $client, string $endPointUri)
    {
        parent::__construct($client, $endPointUri . self::ENDPOINT_URI);
    }

    public function fetchRecommendationEventsSummary(
        int $customerId,
        string $licenseKey,
        DateTimeRange $dateTimeRange
    ): ResponseInterface {
        $uri = $this->buildEndPointUri(
            [
                $customerId,
            ]
        );

        $this->setAuthenticationParameters($customerId, $licenseKey);

        return $this->client->sendRequest(
            Request::METHOD_GET,
            $uri,
            $this->buildOptions($dateTimeRange)
        );
    }

    private function buildOptions(DateTimeRange $dateTimeRange): array
    {
        $interval = $dateTimeRange->getToDate()->diff($dateTimeRange->getFromDate());

        return array_merge(
            [
                'query' => [
                    'fromDate' => $dateTimeRange->getFromDate()->format(DATE_ATOM),
                    'toDate' => $dateTimeRange->getToDate()->format(DATE_ATOM),
                    'duration' => $interval->days < 7 ? self::DURATION_DAILY : self::DURATION_WEEKLY,
                ],
            ],
            $this->getOptions(),
        );
    }
}

class_alias(EventsSummaryDataFetcher::class, 'Ibexa\Platform\Personalization\Client\Consumer\Performance\EventsSummaryDataFetcher');
