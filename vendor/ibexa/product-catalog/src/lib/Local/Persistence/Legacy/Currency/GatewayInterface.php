<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Currency;

use Ibexa\ProductCatalog\Local\Persistence\Values\CurrencyCreateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Values\CurrencyUpdateStruct;

interface GatewayInterface
{
    /**
     * @phpstan-return array{
     *     id: int,
     *     code: non-empty-string,
     *     subunits: int<0, max>,
     *     enabled: bool,
     * }
     */
    public function create(CurrencyCreateStruct $struct): array;

    public function delete(int $id): void;

    /**
     * @phpstan-return array<array{
     *     id: int,
     *     code: non-empty-string,
     *     subunits: int<0, max>,
     *     enabled: bool,
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
     *     code: non-empty-string,
     *     subunits: int<0, max>,
     *     enabled: bool,
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
     *     code: non-empty-string,
     *     subunits: int<0, max>,
     *     enabled: bool,
     * }>
     */
    public function findBy(array $criteria, ?array $orderBy, ?int $limit, int $offset): array;

    /**
     * @phpstan-return array{
     *     id: int,
     *     code: non-empty-string,
     *     subunits: int<0, max>,
     *     enabled: bool,
     * }
     */
    public function update(CurrencyUpdateStruct $struct): array;
}
