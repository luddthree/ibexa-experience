<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\Persistence;

use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Query;

/**
 * @phpstan-type LogEntryData array{
 *     id: string,
 *     group_id: string,
 *     object_id: string,
 *     object_name: string|null,
 *     data: \Ibexa\Contracts\Core\Collection\MapInterface<string, mixed>,
 *     object_class_id: int,
 *     object_class: ObjectClassData,
 *     action_id: int,
 *     action: ActionData,
 * }
 * @phpstan-type Data array{
 *     id: string,
 *     description: string|null,
 *     source: SourceData|null,
 *     source_id: int|null,
 *     ip: IpData|null,
 *     ip_id: int|null,
 *     logged_at: \DateTimeImmutable,
 *     user_id: int,
 *     log_entries: array<LogEntryData>,
 * }
 *
 * @phpstan-import-type Data from \Ibexa\ActivityLog\Persistence\ActivityLog\Action\ActionGatewayInterface as ActionData
 * @phpstan-import-type Data from \Ibexa\ActivityLog\Persistence\ActivityLog\Object\ObjectGatewayInterface as ObjectClassData
 * @phpstan-import-type Data from \Ibexa\ActivityLog\Persistence\ActivityLog\Source\GatewayInterface as SourceData
 * @phpstan-import-type Data from \Ibexa\ActivityLog\Persistence\ActivityLog\Ip\GatewayInterface as IpData
 */
interface HandlerInterface
{
    /**
     * @phpstan-param class-string<object> $objectClass
     * @phpstan-param array<string, mixed> $data
     */
    public function save(
        int $groupId,
        string $objectClass,
        string $objectId,
        string $action,
        ?string $objectName,
        array $data
    ): int;

    /**
     * @phpstan-return Data|null
     */
    public function find(int $id): ?array;

    public function countByQuery(Query $query): int;

    /**
     * @phpstan-return array<Data>
     */
    public function findByQuery(Query $query): array;

    /**
     * @return array<\Ibexa\ActivityLog\Persistence\ActivityLog\Object\ObjectClass>
     */
    public function listObjectClasses(): array;

    /**
     * @return array<\Ibexa\ActivityLog\Persistence\ActivityLog\Action\Action>
     */
    public function listActions(): array;

    /**
     * @param array<\Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\CriterionInterface> $criteria
     */
    public function truncate(array $criteria): void;

    public function initializeGroup(
        int $userId,
        ?string $source = null,
        ?string $ip = null,
        ?string $description = null
    ): int;

    /**
     * @phpstan-return SourceData
     */
    public function loadSource(int $id): array;
}
