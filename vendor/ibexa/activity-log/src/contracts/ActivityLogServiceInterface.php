<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ActivityLog;

use Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActionInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActivityGroupListInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\ObjectClassInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Query;
use Ibexa\Contracts\ActivityLog\Values\CreateActivityLogStruct;

interface ActivityLogServiceInterface
{
    public const ACTION_COPY = 'copy';
    public const ACTION_CREATE = 'create';
    public const ACTION_CREATE_DRAFT = 'create_draft';
    public const ACTION_DELETE = 'delete';
    public const ACTION_DELETE_TRANSLATION = 'delete_translation';
    public const ACTION_HIDE = 'hide';
    public const ACTION_MOVE = 'move';
    public const ACTION_PUBLISH = 'publish';
    public const ACTION_REVEAL = 'reveal';
    public const ACTION_SWAP = 'swap';
    public const ACTION_UPDATE = 'update';
    public const ACTION_RESTORE = 'restore';
    public const ACTION_TRASH = 'trash';

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ForbiddenException
     */
    public function findGroups(Query $query = null): ActivityGroupListInterface;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ForbiddenException
     */
    public function countGroups(Query $query = null): int;

    /**
     * @template T of object
     *
     * @phpstan-param class-string<T> $objectClass
     *
     * @phpstan-return \Ibexa\Contracts\ActivityLog\Values\CreateActivityLogStruct<T>
     */
    public function build(string $objectClass, string $objectId, string $action): CreateActivityLogStruct;

    /**
     * @phpstan-param \Ibexa\Contracts\ActivityLog\Values\CreateActivityLogStruct<object> $struct
     */
    public function save(CreateActivityLogStruct $struct): ?int;

    public function enable(): void;

    public function disable(): void;

    public function isEnabled(): bool;

    public function isDisabled(): bool;

    /**
     * @phpstan-return array<\Ibexa\Contracts\ActivityLog\Values\ActivityLog\ObjectClassInterface<object>>
     */
    public function getObjectClasses(): array;

    /**
     * @phpstan-return \Ibexa\Contracts\ActivityLog\Values\ActivityLog\ObjectClassInterface<object>
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function getObjectClass(int $id): ObjectClassInterface;

    /**
     * @phpstan-return array<\Ibexa\Contracts\ActivityLog\Values\ActivityLog\ActionInterface>
     */
    public function getActions(): array;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function getAction(int $id): ActionInterface;

    public function truncate(): void;

    public function prepareContext(string $source, ?string $description = null): ContextInterface;

    public function dismissContext(): void;
}
