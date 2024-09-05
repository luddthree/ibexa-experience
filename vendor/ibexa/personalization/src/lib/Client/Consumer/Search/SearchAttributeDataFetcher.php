<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\Search;

use Ibexa\Personalization\Client\Consumer\AbstractPersonalizationConsumer;
use Ibexa\Personalization\Client\PersonalizationClientInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;

final class SearchAttributeDataFetcher extends AbstractPersonalizationConsumer implements SearchAttributeDataFetcherInterface
{
    public const ITEMS_KEY = 'items';
    private const ENDPOINT_URI_SUFFIX = '/api/v5/%d/search/attributes';
    private const ROOT_KEY = 'attributeList';

    public function __construct(PersonalizationClientInterface $client, string $endPointUri)
    {
        parent::__construct($client, $endPointUri . self::ENDPOINT_URI_SUFFIX);
    }

    /**
     * @param array<\Ibexa\Personalization\Value\Search\AttributeCriteria> $payload
     *
     * @throws \JsonException
     */
    public function search(
        int $customerId,
        string $licenseKey,
        array $payload
    ): ResponseInterface {
        $uri = $this->buildEndPointUri(
            [
                $customerId,
            ]
        );

        $this->setAuthenticationParameters($customerId, $licenseKey);

        $options = array_merge(
            $this->getOptions(),
            [
                'json' => [
                    self::ROOT_KEY => $payload,
                ],
            ]
        );

        return $this->client->sendRequest(Request::METHOD_POST, $uri, $options);
    }
}
