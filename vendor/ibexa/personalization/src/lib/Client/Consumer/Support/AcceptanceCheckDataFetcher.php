<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\Support;

use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;

final class AcceptanceCheckDataFetcher extends AbstractSupportConsumer implements AcceptanceCheckDataFetcherInterface
{
    private const ENDPOINT_URI = '/personalisation/check-acceptance-status';

    public function __construct(ClientInterface $client, string $endpoint)
    {
        parent::__construct($client, $endpoint . self::ENDPOINT_URI);
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function fetchAcceptanceCheck(string $installationKey): ResponseInterface
    {
        return $this->client->request(
            Request::METHOD_GET,
            $this->endpoint,
            [
                'body' => json_encode([
                    'installationKey' => $installationKey,
                ]),
            ]
        );
    }
}

class_alias(AcceptanceCheckDataFetcher::class, 'Ibexa\Platform\Personalization\Client\Consumer\Support\AcceptanceCheckDataFetcher');
