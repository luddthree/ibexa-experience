<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\OAuth2Client\Repository\Values\User;

final class PasswordHashType
{
    /**
     * @var int Hash type for user registered via OAuth2.
     */
    public const PASSWORD_HASH_OAUTH2 = 256;

    private function __construct()
    {
        /** This class shouldn't be instantiated */
    }
}

class_alias(PasswordHashType::class, 'Ibexa\Platform\Contracts\OAuth2Client\Repository\Values\User\PasswordHashType');
