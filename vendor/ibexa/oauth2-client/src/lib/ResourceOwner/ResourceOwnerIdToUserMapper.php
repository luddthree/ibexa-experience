<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\OAuth2Client\ResourceOwner;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

/**
 * Load user via UserProviderInterface::loadUserByUsername method using ResourceOwnerInterface::getId as username.
 *
 * @see https://github.com/knpuniversity/oauth2-client-bundle#authenticating-any-oauth-user
 */
final class ResourceOwnerIdToUserMapper extends ResourceOwnerToExistingUserMapper
{
    /** @var string|null */
    private $prefix;

    public function __construct(?string $prefix = null)
    {
        $this->prefix = $prefix;
    }

    protected function getUsername(ResourceOwnerInterface $resourceOwner): string
    {
        $username = (string)$resourceOwner->getId();
        if (!empty($this->prefix)) {
            return $username = $this->prefix . $username;
        }

        return $username;
    }
}

class_alias(ResourceOwnerIdToUserMapper::class, 'Ibexa\Platform\OAuth2Client\ResourceOwner\ResourceOwnerIdToUserMapper');
