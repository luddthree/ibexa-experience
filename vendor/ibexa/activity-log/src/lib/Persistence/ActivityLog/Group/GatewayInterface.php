<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\Persistence\ActivityLog\Group;

use Ibexa\Contracts\CorePersistence\Gateway\GatewayInterface as CoreGatewayInterface;

/**
 * @phpstan-type Data array{
 *     id: string,
 *     source_id: int|null,
 *     ip_id: int|null,
 *     description: string|null,
 *     logged_at: \DateTimeImmutable,
 *     user_id: int,
 * }
 *
 * @extends \Ibexa\Contracts\CorePersistence\Gateway\GatewayInterface<Data>
 */
interface GatewayInterface extends CoreGatewayInterface
{
    public function save(
        ?int $userId,
        ?int $sourceId,
        ?int $ipId,
        ?string $description
    ): int;

    /**
     * @param array<\Doctrine\Common\Collections\Expr\Expression|scalar|array<scalar>|null> $criteria
     */
    public function deleteBy(array $criteria): void;
}
