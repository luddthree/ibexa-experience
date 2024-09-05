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

final class PopularityDataFetcher extends AbstractPersonalizationConsumer implements PopularityDataFetcherInterface
{
    public const DURATION_24H = 'VERSION_24H';
    public const DURATION_WEEK = 'VERSION_WEEK';
    public const DURATION_30DAYS = 'VERSION_30DAYS';
    public const DURATION_PREV_MONTH = 'VERSION_PREV_MONTH';

    private const ENDPOINT_URI_SUFFIX = '/api/v1/%d/popularity';

    public function __construct(PersonalizationClientInterface $client, string $endPointUri)
    {
        parent::__construct($client, $endPointUri . self::ENDPOINT_URI_SUFFIX);
    }

    public function fetchPopularityList(
        int $customerId,
        string $licenseKey,
        string $duration
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

    /**
     * @return array<string, array>
     */
    private function buildOptions(string $duration): array
    {
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
