<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\Performance;

use Ibexa\Personalization\Client\Consumer\AbstractPersonalizationConsumer;
use Ibexa\Personalization\Client\PersonalizationClientInterface;
use Ibexa\Personalization\Value\GranularityDateTimeRange;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;

final class EventsDetailsDataFetcher extends AbstractPersonalizationConsumer implements EventsDetailsDataFetcherInterface
{
    private const ENDPOINT_URI = '/api/v4/%d/statistic/summary/REVENUE,RECOS,EVENTS';

    public function __construct(PersonalizationClientInterface $client, string $endPointUri)
    {
        parent::__construct($client, $endPointUri . self::ENDPOINT_URI);
    }

    public function fetchRecommendationEventsDetails(
        int $customerId,
        string $licenseKey,
        GranularityDateTimeRange $granularityDateTimeRange
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
            $this->buildOptions($granularityDateTimeRange)
        );
    }

    private function buildOptions(GranularityDateTimeRange $granularityDateTimeRange): array
    {
        return array_merge(
            [
                'query' => [
                    'granularity' => $granularityDateTimeRange->getGranularity(),
                    'from_date_time' => $granularityDateTimeRange->getFromDate()->format(DATE_ATOM),
                    'to_date_time' => $granularityDateTimeRange->getToDate()->format(DATE_ATOM),
                ],
            ],
            $this->getOptions(),
        );
    }
}

class_alias(EventsDetailsDataFetcher::class, 'Ibexa\Platform\Personalization\Client\Consumer\Performance\EventsDetailsDataFetcher');
