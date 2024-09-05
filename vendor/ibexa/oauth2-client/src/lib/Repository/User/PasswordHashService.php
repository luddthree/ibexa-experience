<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\OAuth2Client\Repository\User;

use Ibexa\Contracts\Core\Repository\PasswordHashService as PasswordHashServiceInterface;
use Ibexa\Contracts\OAuth2Client\Repository\Values\User\PasswordHashType;

/**
 * PasswordHashService decorator which handle PasswordHashType::PASSWORD_HASH_OAUTH2.
 */
final class PasswordHashService implements PasswordHashServiceInterface
{
    /** @var \Ibexa\Core\Repository\User\PasswordHashServiceInterface */
    private $innerPasswordHashService;

    public function __construct(PasswordHashServiceInterface $innerPasswordHashService)
    {
        $this->innerPasswordHashService = $innerPasswordHashService;
    }

    public function getDefaultHashType(): int
    {
        return $this->innerPasswordHashService->getDefaultHashType();
    }

    public function getSupportedHashTypes(): array
    {
        $hashTypes = $this->innerPasswordHashService->getSupportedHashTypes();
        $hashTypes[] = PasswordHashType::PASSWORD_HASH_OAUTH2;

        return $hashTypes;
    }

    public function isHashTypeSupported(int $hashType): bool
    {
        if ($hashType === PasswordHashType::PASSWORD_HASH_OAUTH2) {
            return true;
        }

        return $this->innerPasswordHashService->isHashTypeSupported($hashType);
    }

    public function createPasswordHash(string $plainPassword, ?int $hashType = null): string
    {
        if ($hashType === PasswordHashType::PASSWORD_HASH_OAUTH2) {
            return '';
        }

        return $this->innerPasswordHashService->createPasswordHash($plainPassword, $hashType);
    }

    public function isValidPassword(string $plainPassword, string $passwordHash, ?int $hashType = null): bool
    {
        if ($hashType === PasswordHashType::PASSWORD_HASH_OAUTH2) {
            return false;
        }

        return $this->innerPasswordHashService->isValidPassword($plainPassword, $passwordHash, $hashType);
    }
}

class_alias(PasswordHashService::class, 'Ibexa\Platform\OAuth2Client\Repository\User\PasswordHashService');
