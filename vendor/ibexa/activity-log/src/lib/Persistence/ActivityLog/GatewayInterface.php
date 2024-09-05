<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\Persistence\ActivityLog;

use Ibexa\Contracts\CorePersistence\Gateway\GatewayInterface as CoreGatewayInterface;

/**
 * @phpstan-type Data array{
 *     id: string,
 *     group_id: string,
 *     object_class_id: int,
 *     action_id: int,
 *     object_id: string,
 *     object_name: string|null,
 *     logged_at: \DateTimeImmutable,
 *     user_id: int,
 *     data: array<string, mixed>,
 * }
 *
 * @extends \Ibexa\Contracts\CorePersistence\Gateway\GatewayInterface<Data>
 */
interface GatewayInterface extends CoreGatewayInterface
{
    /**
     * @phpstan-param array<string, mixed> $data
     */
    public function save(
        int $groupId,
        int $objectClassId,
        int $actionId,
        string $objectId,
        ?string $objectName,
        array $data
    ): int;
}
