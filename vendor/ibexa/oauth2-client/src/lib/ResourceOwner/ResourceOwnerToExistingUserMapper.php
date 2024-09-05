<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\OAuth2Client\ResourceOwner;

use Ibexa\Contracts\OAuth2Client\ResourceOwner\ResourceOwnerMapper;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Base class for resource owner mappers based on UserProviderInterface::loadUserByUsername method.
 */
abstract class ResourceOwnerToExistingUserMapper implements ResourceOwnerMapper
{
    final public function getUser(
        ResourceOwnerInterface $resourceOwner,
        UserProviderInterface $userProvider
    ): ?UserInterface {
        return $userProvider->loadUserByUsername(
            $this->getUsername($resourceOwner)
        );
    }

    /**
     * Returns username from given $resourceOwner.
     */
    abstract protected function getUsername(ResourceOwnerInterface $resourceOwner): string;
}

class_alias(ResourceOwnerToExistingUserMapper::class, 'Ibexa\Platform\OAuth2Client\ResourceOwner\ResourceOwnerToExistingUserMapper');
