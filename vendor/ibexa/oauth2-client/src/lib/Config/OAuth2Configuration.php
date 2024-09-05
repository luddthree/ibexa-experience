<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\OAuth2Client\Config;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;

final class OAuth2Configuration implements OAuth2ConfigurationInterface
{
    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface */
    private $configResolver;

    public function __construct(ConfigResolverInterface $configResolver)
    {
        $this->configResolver = $configResolver;
    }

    public function isOAuth2Enabled(): bool
    {
        if ($this->configResolver->hasParameter('oauth2.enabled')) {
            return $this->configResolver->getParameter('oauth2.enabled') === true;
        }

        return false;
    }

    public function isAvailable(string $identifier): bool
    {
        return in_array($identifier, $this->getAvailableClients(), true);
    }

    public function getAvailableClients(): array
    {
        if ($this->configResolver->hasParameter('oauth2.clients')) {
            return $this->configResolver->getParameter('oauth2.clients');
        }

        return [];
    }
}

class_alias(OAuth2Configuration::class, 'Ibexa\Platform\OAuth2Client\Config\OAuth2Configuration');
