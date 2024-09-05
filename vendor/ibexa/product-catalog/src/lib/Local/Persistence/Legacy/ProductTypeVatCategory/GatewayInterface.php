<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductTypeVatCategory;

use Ibexa\ProductCatalog\Local\Persistence\Values\ProductTypeVatCategoryCreateStruct;

interface GatewayInterface
{
    public function insert(ProductTypeVatCategoryCreateStruct $createStruct): int;

    /**
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException
     */
    public function delete(int $id): void;

    /**
     * @param array<string, mixed> $criteria
     */
    public function deleteBy(array $criteria): void;

    /**
     * @phpstan-return array{
     *   id: int,
     *   field_definition_id: int,
     *   region: string,
     *   vat_category: string,
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
     *   id: int,
     *   field_definition_id: int,
     *   region: string,
     *   vat_category: string,
     * }>
     */
    public function findBy(array $criteria, ?array $orderBy, ?int $limit = null, int $offset = 0): array;

    /**
     * @param array<string|int, scalar|array<scalar>|\Doctrine\Common\Collections\Expr\Expression> $criteria
     */
    public function countBy(array $criteria): int;
}
