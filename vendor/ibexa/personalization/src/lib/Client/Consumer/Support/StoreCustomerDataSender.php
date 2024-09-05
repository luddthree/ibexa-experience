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

final class StoreCustomerDataSender extends AbstractSupportConsumer implements StoreCustomerDataSenderInterface
{
    private const ENDPOINT_URI = '/personalisation/store-customer-data';

    public function __construct(ClientInterface $client, string $endpoint)
    {
        parent::__construct($client, $endpoint . self::ENDPOINT_URI);
    }

    public function sendStoreCustomerData(string $installationKey, string $username, string $email): ResponseInterface
    {
        return $this->client->request(
            Request::METHOD_POST,
            $this->endpoint,
            [
                'body' => json_encode([
                    'installationKey' => $installationKey,
                    'username' => $username,
                    'email' => $email,
                ]),
            ]
        );
    }
}

class_alias(StoreCustomerDataSender::class, 'Ibexa\Platform\Personalization\Client\Consumer\Support\StoreCustomerDataSender');
