<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\Recommendation;

use Ibexa\Personalization\Client\Consumer\AbstractPersonalizationConsumer;
use Ibexa\Personalization\Client\PersonalizationClientInterface;
use Ibexa\Personalization\Value\Recommendation\Request as RecommendationRequest;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;

final class RecommendationDataFetcher extends AbstractPersonalizationConsumer implements RecommendationDataFetcherInterface
{
    private const ENDPOINT_URI = '/api/v2/%d/%s/%s';

    public function __construct(PersonalizationClientInterface $client, string $endPointUri)
    {
        parent::__construct($client, $endPointUri . self::ENDPOINT_URI);
    }

    public function fetchRecommendations(
        int $customerId,
        string $licenseKey,
        string $scenarioName,
        RecommendationRequest $recommendationRequest
    ): ResponseInterface {
        $uri = $this->buildEndPointUri(
            [
                $customerId,
                $recommendationRequest->getUserId(),
                $scenarioName,
            ]
        );

        $this->setAuthenticationParameters($customerId, $licenseKey);

        return $this->client->sendRequest(
            Request::METHOD_GET,
            $uri,
            $this->buildOptions($recommendationRequest)
        );
    }

    public function getRecommendationPreviewUri(
        int $customerId,
        string $scenarioName,
        RecommendationRequest $recommendationRequest
    ): string {
        $uri = $this->buildEndPointUri(
            [
                $customerId,
                $recommendationRequest->getUserId(),
                $scenarioName,
            ]
        );

        $query = http_build_query($recommendationRequest->getQueryStringParameters());

        return sprintf(
            '%s?%s',
            $uri->__toString(),
            rawurldecode($query)
        );
    }

    private function buildOptions(RecommendationRequest $recommendationRequest): array
    {
        return
            array_merge(
                [
                    'query' => $recommendationRequest->getQueryStringParameters(),
                ],
                $this->getOptions(),
            );
    }
}

class_alias(RecommendationDataFetcher::class, 'Ibexa\Platform\Personalization\Client\Consumer\Recommendation\RecommendationDataFetcher');
