<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Catalog;

use Ibexa\ProductCatalog\Local\Persistence\Values\CatalogCopyStruct;
use Ibexa\ProductCatalog\Local\Persistence\Values\CatalogCreateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Values\CatalogUpdateStruct;

/**
 * @internal
 */
interface GatewayInterface
{
    public function insert(CatalogCreateStruct $createStruct): int;

    public function update(CatalogUpdateStruct $updateStruct): void;

    public function delete(int $id): void;

    /**
     * @phpstan-return array<array{
     *     id: int,
     *     identifier: string,
     *     creator_id: int,
     *     created: int,
     *     modified: int,
     *     status: string,
     *     query_string: string,
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
     *     creator_id: int,
     *     created: int,
     *     modified: int,
     *     status: string,
     *     query_string: string,
     * }|null
     */
    public function findById(int $id): ?array;

    /**
     * @param array<string|int, scalar|array<scalar>|\Doctrine\Common\Collections\Expr\Expression> $criteria
     * @param array<string, mixed>|null $orderBy
     *
     * @phpstan-return array<array{
     *     id: int,
     *     identifier: string,
     *     creator_id: int,
     *     created: int,
     *     modified: int,
     *     status: string,
     *     query_string: string,
     * }>
     */
    public function findBy(array $criteria, ?array $orderBy, ?int $limit = null, int $offset = 0): array;

    public function copy(CatalogCopyStruct $copyStruct): int;
}
