<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\OAuth2Client\ResourceOwner;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

interface ResourceOwnerMapper
{
    public function getUser(
        ResourceOwnerInterface $resourceOwner,
        UserProviderInterface $userProvider
    ): ?UserInterface;
}

class_alias(ResourceOwnerMapper::class, 'Ibexa\Platform\Contracts\OAuth2Client\ResourceOwner\ResourceOwnerMapper');
