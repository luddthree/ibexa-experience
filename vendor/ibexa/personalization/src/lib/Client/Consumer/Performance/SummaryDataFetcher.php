<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\Performance;

use Ibexa\Personalization\Client\Consumer\AbstractPersonalizationConsumer;
use Ibexa\Personalization\Client\PersonalizationClientInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;

final class SummaryDataFetcher extends AbstractPersonalizationConsumer implements SummaryDataFetcherInterface
{
    private const ENDPOINT_URI = '/api/v5/%d/recommendation/performance/summary';

    public function __construct(PersonalizationClientInterface $client, string $endPointUri)
    {
        parent::__construct($client, $endPointUri . self::ENDPOINT_URI);
    }

    public function fetchRecommendationSummary(
        int $customerId,
        string $licenseKey,
        ?string $duration = null
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
            $this->buildOptions($duration)
        );
    }

    private function buildOptions(?string $duration = null): array
    {
        if (null === $duration) {
            return $this->getOptions();
        }

        return array_merge(
            [
                'query' => [
                    'duration' => $duration,
                ],
            ],
            $this->getOptions()
        );
    }
}

class_alias(SummaryDataFetcher::class, 'Ibexa\Platform\Personalization\Client\Consumer\Performance\SummaryDataFetcher');
