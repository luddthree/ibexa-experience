<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog;

use Ibexa\Contracts\ActivityLog\ContextInterface;
use Ibexa\Contracts\ActivityLog\Exception\ContextLockedException;

final class Context implements ContextInterface
{
    private int $id;

    /** @readonly */
    private ?string $source;

    private ?string $description = null;

    private int $userId;

    private ?string $ip = null;

    public function __construct(?string $source = null)
    {
        $this->source = $source;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->assertNotLocked();
        $this->id = $id;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(?string $source): void
    {
        $this->source = $source;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->assertNotLocked();
        $this->description = $description;
    }

    public function hasUserId(): bool
    {
        return isset($this->userId);
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->assertNotLocked();
        $this->userId = $userId;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(?string $ip): void
    {
        $this->assertNotLocked();
        $this->ip = $ip;
    }

    private function assertNotLocked(): void
    {
        if ($this->isPersisted()) {
            throw new ContextLockedException(sprintf(
                '%s is locked (persisted) and cannot be changed.',
                self::class,
            ));
        }
    }

    public function isPersisted(): bool
    {
        return isset($this->id);
    }
}
