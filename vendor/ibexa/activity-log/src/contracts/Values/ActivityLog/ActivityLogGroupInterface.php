<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ActivityLog\Values\ActivityLog;

use DateTimeInterface;
use Ibexa\Contracts\Core\Repository\Values\User\User;

interface ActivityLogGroupInterface
{
    public function getSource(): ?ActivityLogSourceInterface;

    public function getIp(): ?ActivityLogIpInterface;

    public function getDescription(): ?string;

    /**
     * @return array<\Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActivityLogInterface<object>>
     */
    public function getActivityLogs(): array;

    public function setUser(User $user): void;

    /**
     * This method can return null if the user is not accessible anymore, or current user does not have permissions to
     * access it.
     */
    public function getUser(): ?User;

    public function getUserId(): int;

    public function getLoggedAt(): DateTimeInterface;
}
