<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\User;

use Ibexa\Personalization\Client\Consumer\AbstractPersonalizationConsumer;
use Ibexa\Personalization\Client\PersonalizationClientInterface;
use Ibexa\Personalization\SPI\UserAPIRequest;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;

final class UpdateUserDataSender extends AbstractPersonalizationConsumer implements UpdateUserDataSenderInterface
{
    private const ENDPOINT_URI_SUFFIX = '/api/%d/v2/%s/user';

    public function __construct(PersonalizationClientInterface $client, string $endPointUri)
    {
        parent::__construct($client, $endPointUri . self::ENDPOINT_URI_SUFFIX);
    }

    public function updateUserAttributes(UserAPIRequest $request): ResponseInterface
    {
        $endPointUri = $this->buildEndPointUri([
            $this->client->getCustomerId(),
            $request->source,
        ]);

        return $this->client->sendRequest(Request::METHOD_POST, $endPointUri, [
            'body' => $request->xmlBody,
            'headers' => [
                'Content-Type' => 'text/xml',
                'Authorization' => 'Basic ' . base64_encode($this->client->getCustomerId() . ':' . $this->client->getLicenseKey()),
            ],
        ]);
    }
}
