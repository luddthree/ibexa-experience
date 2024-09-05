<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\ModelBuild;

use Ibexa\Personalization\Client\Consumer\AbstractPersonalizationConsumer;
use Ibexa\Personalization\Client\PersonalizationClientInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
final class ModelBuildDataFetcher extends AbstractPersonalizationConsumer implements ModelBuildDataFetcherInterface
{
    private const ENDPOINT_URL_SUFFIX = '/api/v3/%d/modelbuild/get_model/%s';
    private const PARAM_LAST_BUILDS_NUMBER = 'lastBuildsNum';

    public function __construct(PersonalizationClientInterface $client, string $endPointUri)
    {
        parent::__construct($client, $endPointUri . self::ENDPOINT_URL_SUFFIX);
    }

    public function getModelBuildStatus(
        int $customerId,
        string $licenseKey,
        string $modelId,
        int $lastBuildsNumber
    ): ResponseInterface {
        $uri = $this->buildEndPointUri(
            [
                $customerId,
                $modelId,
            ]
        );

        $this->setAuthenticationParameters($customerId, $licenseKey);

        return $this->client->sendRequest(
            Request::METHOD_GET,
            $uri,
            array_merge(
                [
                    'query' => [
                        self::PARAM_LAST_BUILDS_NUMBER => $lastBuildsNumber,
                    ],
                ],
                $this->getOptions(),
            )
        );
    }
}
