<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\Account;

use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
final class AccountDataSender implements AccountDataSenderInterface
{
    private const ENDPOINT_URL_SUFFIX = '/personalisation/mandators';

    private ClientInterface $client;

    private string $endpoint;

    public function __construct(
        ClientInterface $client,
        string $endpoint
    ) {
        $this->client = $client;
        $this->endpoint = $endpoint . self::ENDPOINT_URL_SUFFIX;
    }

    public function createAccount(
        string $installationKey,
        string $name,
        string $template
    ): ResponseInterface {
        return $this->client->request(
            Request::METHOD_POST,
            $this->endpoint,
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'installationKey' => $installationKey,
                    'mandatorName' => $name,
                    'template' => $template,
                ],
            ]
        );
    }
}
