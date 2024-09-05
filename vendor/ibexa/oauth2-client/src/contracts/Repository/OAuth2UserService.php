<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\OAuth2Client\Repository;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\Core\Repository\Values\User\UserCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\User\UserUpdateStruct;

interface OAuth2UserService
{
    /**
     * Instantiate a OAuth2 user create struct class.
     */
    public function newOAuth2UserCreateStruct(
        string $login,
        string $email,
        string $mainLanguageCode,
        ?ContentType $contentType = null
    ): UserCreateStruct;

    /**
     * Instantiate a new OAuth2 user update struct.
     */
    public function newOAuth2UserUpdateStruct(): UserUpdateStruct;

    /**
     * Return true if given user is created via OAuth2.
     */
    public function isOAuth2User(User $user): bool;
}

class_alias(OAuth2UserService::class, 'Ibexa\Platform\Contracts\OAuth2Client\Repository\OAuth2UserService');
