<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\OAuth2Client\Exception\ResourceOwner;

use Ibexa\Contracts\OAuth2Client\Exception\OAuth2ClientException;
use Ibexa\Contracts\OAuth2Client\ResourceOwner\ResourceOwnerMapper;
use InvalidArgumentException;
use Throwable;

final class MapperNotFoundException extends InvalidArgumentException implements OAuth2ClientException
{
    /** @var string */
    private $client;

    public function __construct(string $client, int $code = 0, Throwable $previous = null)
    {
        parent::__construct(
            sprintf('%s not found for client %s', ResourceOwnerMapper::class, $client),
            $code,
            $previous
        );

        $this->client = $client;
    }

    public function getClient(): string
    {
        return $this->client;
    }
}

class_alias(MapperNotFoundException::class, 'Ibexa\Platform\Contracts\OAuth2Client\Exception\ResourceOwner\MapperNotFoundException');
