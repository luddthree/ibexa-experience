<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\OAuth2Client\Repository\User;

use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\OAuth2Client\Repository\Values\User\PasswordHashType;
use Ibexa\Core\Repository\User\PasswordHashServiceInterface;
use Ibexa\OAuth2Client\Repository\User\PasswordHashService;
use PHPUnit\Framework\TestCase;

final class PasswordHashServiceTest extends TestCase
{
    private const EXAMPLE_PLAIN_PASSWORD = 'publish';
    private const EXAMPLE_PASSWORD_HASH = '$2y$10$FDn9NPwzhq85cLLxfD5Wu.L3SL3Z/LNCvhkltJUV0wcJj7ciJg2oy';

    /** @var \Ibexa\Core\Repository\User\PasswordHashServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $innerPasswordHashService;

    /** @var \Ibexa\OAuth2Client\Repository\User\PasswordHashService */
    private $passwordHashService;

    protected function setUp(): void
    {
        $this->innerPasswordHashService = $this->createMock(PasswordHashServiceInterface::class);
        $this->passwordHashService = new PasswordHashService($this->innerPasswordHashService);
    }

    public function testGetDefaultHashType(): void
    {
        $this->innerPasswordHashService
            ->method('getDefaultHashType')
            ->willReturn(User::DEFAULT_PASSWORD_HASH);

        self::assertEquals(
            User::DEFAULT_PASSWORD_HASH,
            $this->passwordHashService->getDefaultHashType()
        );
    }

    public function testGetSupportedHashTypes(): void
    {
        $this->innerPasswordHashService
            ->method('getSupportedHashTypes')
            ->willReturn([User::DEFAULT_PASSWORD_HASH]);

        self::assertEquals(
            [
                User::DEFAULT_PASSWORD_HASH,
                PasswordHashType::PASSWORD_HASH_OAUTH2,
            ],
            $this->passwordHashService->getSupportedHashTypes()
        );
    }

    public function testIsHashTypeSupportForOAuth2HashType(): void
    {
        $isSupportedOAuth2HashType = $this
            ->passwordHashService
            ->isHashTypeSupported(PasswordHashType::PASSWORD_HASH_OAUTH2);

        self::assertTrue($isSupportedOAuth2HashType);
    }

    public function testIsHashTypeSupportedIsDelegatedToInnerService(): void
    {
        $this->innerPasswordHashService
            ->method('isHashTypeSupported')
            ->with(User::DEFAULT_PASSWORD_HASH)
            ->willReturn(true);

        $isSupportedDefaultHashType = $this
            ->passwordHashService
            ->isHashTypeSupported(User::DEFAULT_PASSWORD_HASH);

        self::assertTrue($isSupportedDefaultHashType);
    }

    public function testCreatePasswordHashForOAuth2HashType(): void
    {
        $actualPasswordHash = $this->passwordHashService->createPasswordHash(
            'whatever',
            PasswordHashType::PASSWORD_HASH_OAUTH2
        );

        self::assertEquals('', $actualPasswordHash);
    }

    public function testCreatePasswordHashIsDelegatedToInnerService(): void
    {
        $createPasswordHashArgs = [self::EXAMPLE_PLAIN_PASSWORD, User::DEFAULT_PASSWORD_HASH];

        $this->innerPasswordHashService
            ->method('createPasswordHash')
            ->with(...$createPasswordHashArgs)
            ->willReturn(self::EXAMPLE_PASSWORD_HASH);

        self::assertEquals(
            self::EXAMPLE_PASSWORD_HASH,
            $this->passwordHashService->createPasswordHash(...$createPasswordHashArgs)
        );
    }

    public function testIsValidPasswordForOAuth2HashType(): void
    {
        $isValidPassword = $this->passwordHashService->isValidPassword(
            self::EXAMPLE_PLAIN_PASSWORD,
            'whatever',
            PasswordHashType::PASSWORD_HASH_OAUTH2
        );

        self::assertFalse($isValidPassword);
    }

    public function testIsValidPasswordIsDelegatedToInnerService(): void
    {
        $isValidPasswordArgs = [
            self::EXAMPLE_PLAIN_PASSWORD,
            self::EXAMPLE_PASSWORD_HASH,
            User::DEFAULT_PASSWORD_HASH,
        ];

        $this->innerPasswordHashService
            ->method('isValidPassword')
            ->with(...$isValidPasswordArgs)
            ->willReturn(true);

        self::assertTrue($this->passwordHashService->isValidPassword(...$isValidPasswordArgs));
    }
}

class_alias(PasswordHashServiceTest::class, 'Ibexa\Platform\Tests\OAuth2Client\Repository\User\PasswordHashServiceTest');
