<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\OAuth2Client\ResourceOwner;

use Ibexa\OAuth2Client\ResourceOwner\ResourceOwnerEmailToUserMapper;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class ResourceOwnerEmailToUserMapperTest extends TestCase
{
    public function testGetUser(): void
    {
        $expectedUser = $this->createMock(UserInterface::class);

        $resourceOwner = $this->createExampleResourceOwner();

        $userProvider = $this->createMock(UserProviderInterface::class);
        $userProvider
            ->method('loadUserByUsername')
            ->with('user@ibexa.co')
            ->willReturn($expectedUser);

        $mapper = new ResourceOwnerEmailToUserMapper('email');
        self::assertEquals(
            $expectedUser,
            $mapper->getUser($resourceOwner, $userProvider)
        );
    }

    private function createExampleResourceOwner(): ResourceOwnerInterface
    {
        return new class() implements ResourceOwnerInterface {
            public function getId(): string
            {
                return '115302786425775563093';
            }

            public function getEmail(): string
            {
                return 'user@ibexa.co';
            }

            public function toArray(): array
            {
                return [
                    'id' => '115302786425775563093',
                    'email' => 'user@ibexa.co',
                ];
            }
        };
    }
}

class_alias(ResourceOwnerEmailToUserMapperTest::class, 'Ibexa\Platform\Tests\OAuth2Client\ResourceOwner\ResourceOwnerEmailToUserMapperTest');
