<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\OAuth2Client\Exception\Client;

use Ibexa\Contracts\OAuth2Client\Exception\OAuth2ClientException;
use RuntimeException;
use Throwable;

final class UnavailableClientException extends RuntimeException implements OAuth2ClientException
{
    /** @var string */
    private $client;

    public function __construct(string $client, int $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('Client %s is not available for current scope', $client), $code, $previous);

        $this->client = $client;
    }

    public function getClient(): string
    {
        return $this->client;
    }
}

class_alias(UnavailableClientException::class, 'Ibexa\Platform\Contracts\OAuth2Client\Exception\Client\UnavailableClientException');
