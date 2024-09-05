<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\OAuth2Client\Client;

use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;

interface ClientRegistry
{
    /**
     * @throws \Ibexa\Contracts\OAuth2Client\Exception\Client\DisabledClientException
     * @throws \Ibexa\Contracts\OAuth2Client\Exception\Client\UnavailableClientException
     */
    public function getClient(string $identifier): OAuth2ClientInterface;

    public function hasClient(string $identifier): bool;
}

class_alias(ClientRegistry::class, 'Ibexa\Platform\Contracts\OAuth2Client\Client\ClientRegistry');
