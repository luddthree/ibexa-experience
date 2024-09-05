<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\OAuth2Client\Repository;

use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\Core\Repository\Values\User\UserCreateStruct;
use Ibexa\Contracts\OAuth2Client\Repository\OAuth2UserService;
use Ibexa\Contracts\OAuth2Client\Repository\Values\User\PasswordHashType;
use Ibexa\Tests\Integration\Core\Repository\BaseTest;

final class OAuth2UserServiceTest extends BaseTest
{
    private const ADMIN_USER_GROUP_ID = 12;
    private const ADMIN_USER_LOGIN = 'admin';

    public function testNewOAuth2UserCreateStruct(): void
    {
        $newUserCreateStruct = $this
            ->getOAuth2UserService()
            ->newOAuth2UserCreateStruct(
                'user',
                'user@example.com',
                'eng-US'
            );

        $this->assertInstanceOf(UserCreateStruct::class, $newUserCreateStruct);
        self::assertEquals('user', $newUserCreateStruct->login);
        self::assertEquals('user@example.com', $newUserCreateStruct->email);
        self::assertEquals('eng-US', $newUserCreateStruct->mainLanguageCode);
    }

    public function testCreateOAuth2User(): User
    {
        $userService = $this->getOAuth2UserService();

        $newUserCreateStruct = $userService->newOAuth2UserCreateStruct(
            'user',
            'user@example.com',
            'eng-US'
        );

        $newUserCreateStruct->setField('first_name', 'John');
        $newUserCreateStruct->setField('last_name', 'Doe');

        $newUser = $userService->createUser($newUserCreateStruct, [
            $userService->loadUserGroup(self::ADMIN_USER_GROUP_ID),
        ]);

        self::assertEquals('user', $newUser->login);
        self::assertEquals('user@example.com', $newUser->email);
        self::assertEquals('eng-US', $newUser->contentInfo->mainLanguageCode);

        $this->assertOAuth2Password($newUser);

        return $newUser;
    }

    /**
     * @depends Ibexa\Tests\Integration\OAuth2Client\Repository\OAuth2UserServiceTest::testCreateOAuth2User
     */
    public function testIsOAuth2User(User $user): void
    {
        self::assertTrue($this->getOAuth2UserService()->isOAuth2User($user));
    }

    /**
     * @depends Ibexa\Tests\Integration\OAuth2Client\Repository\OAuth2UserServiceTest::testCreateOAuth2User
     */
    public function testUserOAuth2User(User $user): void
    {
        $userService = $this->getOAuth2UserService();

        $updateStruct = $userService->newOAuth2UserUpdateStruct();
        $updateStruct->password = 'P@ssword!';

        $updatedUser = $userService->updateUser($user, $updateStruct);

        $this->assertOAuth2Password($updatedUser);
    }

    public function testIsNotOAuth2User(): void
    {
        $userService = $this->getOAuth2UserService();
        $adminUser = $userService->loadUserByLogin(self::ADMIN_USER_LOGIN);

        self::assertFalse($userService->isOAuth2User($adminUser));
    }

    private function assertOAuth2Password(
        User $user,
        string $userAccountFieldDefIdentifier = 'user_account'
    ): void {
        self::assertEquals('', $user->passwordHash);
        self::assertEquals(
            PasswordHashType::PASSWORD_HASH_OAUTH2,
            $user->getFieldValue($userAccountFieldDefIdentifier)->passwordHashType
        );
    }

    /**
     * @return \Ibexa\Contracts\OAuth2Client\Repository\OAuth2UserService|\Ibexa\Contracts\Core\Repository\UserService
     */
    private function getOAuth2UserService(): OAuth2UserService
    {
        return $this->getSetupFactory()->getServiceContainer()->get(OAuth2UserService::class);
    }
}
class_alias(OAuth2UserServiceTest::class, 'Ibexa\Platform\Tests\Integration\OAuth2Client\Repository\OAuth2UserServiceTest');
