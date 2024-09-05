<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Service\User;

use Ibexa\Personalization\SPI\UserAPIRequest;

/**
 * @internal
 */
interface UserServiceInterface
{
    public function updateUser(UserAPIRequest $userAPIRequest): int;
}
