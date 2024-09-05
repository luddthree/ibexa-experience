<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\OAuth2Client\Repository;

use Ibexa\Contracts\Core\Repository\Decorator\UserServiceDecorator;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\Core\Repository\Values\User\UserCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\User\UserUpdateStruct;
use Ibexa\Contracts\OAuth2Client\Repository\OAuth2UserService as OAuth2UserServiceInterface;
use Ibexa\Contracts\OAuth2Client\Repository\Values\User\PasswordHashType;
use Ibexa\Core\FieldType\User\Value;

final class OAuth2UserService extends UserServiceDecorator implements OAuth2UserServiceInterface
{
    public function newOAuth2UserCreateStruct(
        string $login,
        string $email,
        string $mainLanguageCode,
        ?ContentType $contentType = null
    ): UserCreateStruct {
        $userCreateStruct = $this->innerService->newUserCreateStruct(
            $login,
            $email,
            'none',
            $mainLanguageCode,
            $contentType
        );

        foreach ($userCreateStruct->fields as $field) {
            if ($field->value instanceof Value) {
                $field->value->passwordHashType = PasswordHashType::PASSWORD_HASH_OAUTH2;
            }
        }

        return $userCreateStruct;
    }

    public function newOAuth2UserUpdateStruct(): UserUpdateStruct
    {
        return $this->innerService->newUserUpdateStruct();
    }

    public function isOAuth2User(User $user): bool
    {
        foreach ($user->fields as $field) {
            $value = $field->value;
            if ($value instanceof Value && $this->hasOAuth2HashType($value)) {
                return true;
            }
        }

        return false;
    }

    private function hasOAuth2HashType(Value $userValue): bool
    {
        return (int)$userValue->passwordHashType === PasswordHashType::PASSWORD_HASH_OAUTH2;
    }
}

class_alias(OAuth2UserService::class, 'Ibexa\Platform\OAuth2Client\Repository\OAuth2UserService');
