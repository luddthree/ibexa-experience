<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Service\User;

use Ibexa\Personalization\Client\Consumer\User\UpdateUserDataSenderInterface;
use Ibexa\Personalization\SPI\UserAPIRequest;

final class UserService implements UserServiceInterface
{
    private UpdateUserDataSenderInterface $updateUserDataSender;

    public function __construct(UpdateUserDataSenderInterface $updateUserDataSender)
    {
        $this->updateUserDataSender = $updateUserDataSender;
    }

    public function updateUser(UserAPIRequest $userAPIRequest): int
    {
        $response = $this->updateUserDataSender->updateUserAttributes($userAPIRequest);

        return $response->getStatusCode();
    }
}
