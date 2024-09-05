<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\OAuth2Client\Client;

use Ibexa\Contracts\OAuth2Client\Client\ClientRegistry as ClientRegistryInterface;
use Ibexa\Contracts\OAuth2Client\Exception\Client\DisabledClientException;
use Ibexa\Contracts\OAuth2Client\Exception\Client\UnavailableClientException;
use Ibexa\OAuth2Client\Config\OAuth2ConfigurationInterface;
use InvalidArgumentException;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry as KnpClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;

final class ClientRegistry implements ClientRegistryInterface
{
    /** @var \KnpU\OAuth2ClientBundle\Client\ClientRegistry */
    private $innerClientRegistry;

    /** @var \Ibexa\OAuth2Client\Config\OAuth2ConfigurationInterface */
    private $configuration;

    public function __construct(KnpClientRegistry $innerClientRegistry, OAuth2ConfigurationInterface $configuration)
    {
        $this->innerClientRegistry = $innerClientRegistry;
        $this->configuration = $configuration;
    }

    public function getClient(string $identifier): OAuth2ClientInterface
    {
        if (!$this->configuration->isOAuth2Enabled()) {
            throw new DisabledClientException();
        }

        if (!$this->configuration->isAvailable($identifier)) {
            throw new UnavailableClientException($identifier);
        }

        try {
            return $this->innerClientRegistry->getClient($identifier);
        } catch (InvalidArgumentException $e) {
            throw new UnavailableClientException($identifier);
        }
    }

    public function hasClient(string $identifier): bool
    {
        if (!$this->configuration->isOAuth2Enabled()) {
            return false;
        }

        return $this->configuration->isAvailable($identifier);
    }
}

class_alias(ClientRegistry::class, 'Ibexa\Platform\OAuth2Client\Client\ClientRegistry');
