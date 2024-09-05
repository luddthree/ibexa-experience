<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Connect\MessageHandler;

use Ibexa\Bundle\Connect\Message\WebhookRequest;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class WebhookRequestHandler
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function handle(WebhookRequest $notification): void
    {
        $this->httpClient->request('POST', $notification->getUrl(), [
            'json' => $notification->getData(),
        ]);
    }
}
