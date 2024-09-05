<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog;

use DateTimeInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActivityLogGroupInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActivityLogIpInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActivityLogSourceInterface;
use Ibexa\Contracts\Core\Repository\Values\User\User;

final class ActivityLogGroup implements ActivityLogGroupInterface
{
    private string $id;

    private ?string $description;

    private ?ActivityLogSourceInterface $source;

    private ?ActivityLogIpInterface $ip;

    private DateTimeInterface $loggedAt;

    private int $userId;

    /** @var \Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActivityLogInterface<object>[] */
    private array $logEntries;

    private ?User $user = null;

    /**
     * @phpstan-param \Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActivityLogInterface<object>[] $logEntries
     */
    public function __construct(
        string $id,
        ?string $description,
        ?ActivityLogSourceInterface $source,
        ?ActivityLogIpInterface $ip,
        DateTimeInterface $loggedAt,
        int $userId,
        array $logEntries
    ) {
        $this->id = $id;
        $this->description = $description;
        $this->source = $source;
        $this->ip = $ip;
        $this->loggedAt = $loggedAt;
        $this->userId = $userId;
        $this->logEntries = $logEntries;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getSource(): ?ActivityLogSourceInterface
    {
        return $this->source;
    }

    public function getIp(): ?ActivityLogIpInterface
    {
        return $this->ip;
    }

    public function getLoggedAt(): DateTimeInterface
    {
        return $this->loggedAt;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getActivityLogs(): array
    {
        return $this->logEntries;
    }
}
