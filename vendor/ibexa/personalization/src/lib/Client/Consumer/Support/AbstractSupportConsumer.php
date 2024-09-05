<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\Support;

use GuzzleHttp\ClientInterface;

abstract class AbstractSupportConsumer
{
    /** @var \GuzzleHttp\ClientInterface */
    protected $client;

    /** @var string */
    protected $endpoint;

    public function __construct(
        ClientInterface $client,
        string $endpoint
    ) {
        $this->client = $client;
        $this->endpoint = $endpoint;
    }
}

class_alias(AbstractSupportConsumer::class, 'Ibexa\Platform\Personalization\Client\Consumer\Support\AbstractSupportConsumer');
