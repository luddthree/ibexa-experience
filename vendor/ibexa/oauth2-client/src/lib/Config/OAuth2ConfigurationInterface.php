<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\OAuth2Client\Config;

/**
 * @internal
 */
interface OAuth2ConfigurationInterface
{
    /**
     * Returns true if OAuth2 is enabled for current scope.
     */
    public function isOAuth2Enabled(): bool;

    /**
     * Returns true if client with given identifier is available for current scope.
     */
    public function isAvailable(string $identifier): bool;

    /**
     * Returns identifiers of clients available in current scope.
     *
     * @return string[]
     */
    public function getAvailableClients(): array;
}

class_alias(OAuth2ConfigurationInterface::class, 'Ibexa\Platform\OAuth2Client\Config\OAuth2ConfigurationInterface');
