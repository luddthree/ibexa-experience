<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\OAuth2Client\ResourceOwner;

use Ibexa\OAuth2Client\ResourceOwner\ResourceOwnerIdToUserMapper;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class ResourceOwnerIdToUserMapperTest extends TestCase
{
    private const EXAMPLE_PREFIX = 'google:';
    private const EXAMPLE_RESOURCE_OWNER_ID = '115302786425775563093';

    public function testGetUser(): void
    {
        $expectedUser = $this->createMock(UserInterface::class);

        $resourceOwner = $this->createMock(ResourceOwnerInterface::class);
        $resourceOwner->method('getId')->willReturn(self::EXAMPLE_RESOURCE_OWNER_ID);

        $userProvider = $this->createMock(UserProviderInterface::class);
        $userProvider
            ->method('loadUserByUsername')
            ->with(self::EXAMPLE_PREFIX . self::EXAMPLE_RESOURCE_OWNER_ID)
            ->willReturn($expectedUser);

        $mapper = new ResourceOwnerIdToUserMapper(self::EXAMPLE_PREFIX);
        self::assertEquals(
            $expectedUser,
            $mapper->getUser($resourceOwner, $userProvider)
        );
    }
}

class_alias(ResourceOwnerIdToUserMapperTest::class, 'Ibexa\Platform\Tests\OAuth2Client\ResourceOwner\ResourceOwnerIdToUserMapperTest');
