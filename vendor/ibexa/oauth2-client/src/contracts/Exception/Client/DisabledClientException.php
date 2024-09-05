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

final class DisabledClientException extends RuntimeException implements OAuth2ClientException
{
    public function __construct(?string $message = null, int $code = 0, Throwable $previous = null)
    {
        if ($message === null) {
            $message = 'OAuth2 is not enabled for current scope';
        }

        parent::__construct($message, $code, $previous);
    }
}

class_alias(DisabledClientException::class, 'Ibexa\Platform\Contracts\OAuth2Client\Exception\Client\DisabledClientException');
