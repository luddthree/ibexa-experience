<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\CustomerGroup;

use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupUpdateStruct;

/**
 * @internal
 */
interface GatewayInterface
{
    public function insert(CustomerGroupCreateStruct $createStruct): int;

    public function update(CustomerGroupUpdateStruct $updateStruct): void;

    public function delete(int $id): void;

    /**
     * @phpstan-return array<array{
     *     id: int,
     *     identifier: string,
     *     global_price_rate: numeric-string,
     * }>
     */
    public function findAll(?int $limit = null, int $offset = 0): array;

    public function countAll(): int;

    /**
     * @param array<string|int, scalar|array<scalar>|\Doctrine\Common\Collections\Expr\Expression> $criteria
     */
    public function countBy(array $criteria): int;

    /**
     * @phpstan-return array{
     *     id: int,
     *     identifier: string,
     *     global_price_rate: numeric-string,
     * }|null
     */
    public function findById(int $id): ?array;

    /**
     * @param array<string|int, scalar|array<scalar>|\Doctrine\Common\Collections\Expr\Expression> $criteria
     * @param array<string, mixed>|null $orderBy
     * @param int|null $limit
     * @param int $offset
     *
     * @phpstan-return array<array{
     *     id: int,
     *     identifier: string,
     *     global_price_rate: numeric-string,
     * }>
     */
    public function findBy(array $criteria, ?array $orderBy, ?int $limit, int $offset): array;
}
