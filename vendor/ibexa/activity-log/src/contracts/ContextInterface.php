<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ActivityLog;

interface ContextInterface
{
    public function getId(): int;

    /**
     * @throws \Ibexa\Contracts\ActivityLog\Exception\ContextLockedException
     */
    public function setId(int $id): void;

    public function getSource(): ?string;

    /**
     * @throws \Ibexa\Contracts\ActivityLog\Exception\ContextLockedException
     */
    public function setSource(?string $source): void;

    public function getDescription(): ?string;

    /**
     * @throws \Ibexa\Contracts\ActivityLog\Exception\ContextLockedException
     */
    public function setDescription(?string $description): void;

    public function hasUserId(): bool;

    public function getUserId(): int;

    /**
     * @throws \Ibexa\Contracts\ActivityLog\Exception\ContextLockedException
     */
    public function setUserId(int $userId): void;

    public function getIp(): ?string;

    /**
     * @throws \Ibexa\Contracts\ActivityLog\Exception\ContextLockedException
     */
    public function setIp(?string $ip): void;

    public function isPersisted(): bool;
}
