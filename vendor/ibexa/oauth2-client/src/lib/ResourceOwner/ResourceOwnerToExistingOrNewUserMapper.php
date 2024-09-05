<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\OAuth2Client\ResourceOwner;

use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\OAuth2Client\ResourceOwner\ResourceOwnerMapper;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

abstract class ResourceOwnerToExistingOrNewUserMapper implements ResourceOwnerMapper
{
    /** @var \Ibexa\Contracts\Core\Repository\Repository */
    protected $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function getUser(
        ResourceOwnerInterface $resourceOwner,
        UserProviderInterface $userProvider
    ): ?UserInterface {
        try {
            return $this->loadUser($resourceOwner, $userProvider);
        } catch (UsernameNotFoundException $e) {
            $callable = function (Repository $repository) use (
                $resourceOwner,
                $userProvider
            ): UserInterface {
                return $this->createUser($resourceOwner, $userProvider);
            };

            return $this->repository->sudo($callable);
        }
    }

    /**
     * Loads existing user from given $resourceOwner.
     */
    abstract protected function loadUser(
        ResourceOwnerInterface $resourceOwner,
        UserProviderInterface $userProvider
    ): ?UserInterface;

    /**
     * Create new user from given $resourceOwner.
     */
    abstract protected function createUser(
        ResourceOwnerInterface $resourceOwner,
        UserProviderInterface $userProvider
    ): ?UserInterface;
}

class_alias(ResourceOwnerToExistingOrNewUserMapper::class, 'Ibexa\Platform\OAuth2Client\ResourceOwner\ResourceOwnerToExistingOrNewUserMapper');
