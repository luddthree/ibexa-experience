<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\OAuth2Client\Client;

use Ibexa\OAuth2Client\Config\OAuth2ConfigurationInterface;

/**
 * Simplified OAuth2ConfigurationInterface impl. for test proposes.
 */
final class OAuth2Configuration implements OAuth2ConfigurationInterface
{
    /** @var bool */
    private $isOAuth2Enabled;

    /** @var string[] */
    private $availableClients;

    /**
     * @param string[] $availableClients
     */
    public function __construct(
        bool $isOAuth2Enabled = false,
        array $availableClients = []
    ) {
        $this->isOAuth2Enabled = $isOAuth2Enabled;
        $this->availableClients = $availableClients;
    }

    public function isOAuth2Enabled(): bool
    {
        return $this->isOAuth2Enabled;
    }

    public function isAvailable(string $identifier): bool
    {
        return in_array($identifier, $this->availableClients);
    }

    /**
     * @return string[]
     */
    public function getAvailableClients(): array
    {
        return $this->availableClients;
    }
}

class_alias(OAuth2Configuration::class, 'Ibexa\Platform\Tests\OAuth2Client\Client\OAuth2Configuration');
