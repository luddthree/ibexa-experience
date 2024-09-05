<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ActivityLog\Values;

/**
 * @template-covariant T of object
 */
final class CreateActivityLogStruct
{
    /** @phpstan-var class-string<T> */
    private string $objectClass;

    private string $objectId;

    private string $action;

    private int $userId;

    private ?string $objectName;

    /** @phpstan-var array<string, mixed> */
    private array $data;

    /**
     * @phpstan-param class-string<T> $objectClass
     *
     * @param array<string, mixed> $data
     */
    public function __construct(
        string $objectClass,
        string $objectId,
        string $action,
        int $userId,
        ?string $objectName = null,
        array $data = []
    ) {
        $this->objectClass = $objectClass;
        $this->objectId = $objectId;
        $this->action = $action;
        $this->userId = $userId;
        $this->objectName = $objectName;
        $this->data = $data;
    }

    /**
     * @phpstan-return class-string<T>
     */
    public function getObjectClass(): string
    {
        return $this->objectClass;
    }

    public function getObjectId(): string
    {
        return $this->objectId;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setObjectName(string $objectName): void
    {
        $this->objectName = $objectName;
    }

    public function getObjectName(): ?string
    {
        return $this->objectName;
    }

    /**
     * @phpstan-param array<string, mixed> $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * @phpstan-return array<string, mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }
}
