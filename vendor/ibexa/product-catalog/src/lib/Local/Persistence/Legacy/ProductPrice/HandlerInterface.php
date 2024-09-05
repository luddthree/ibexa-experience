<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice;

use Ibexa\Contracts\ProductCatalog\Values\Price\Create\Struct\ProductPriceCreateStructInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\Update\Struct\ProductPriceUpdateStructInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\AbstractProductPrice;

interface HandlerInterface
{
    public function create(ProductPriceCreateStructInterface $createStruct): int;

    /**
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException
     */
    public function update(ProductPriceUpdateStructInterface $updateStruct): void;

    /**
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException
     */
    public function delete(int $id): void;

    /**
     * @param array<string, \Doctrine\Common\Collections\Expr\Expression|scalar|array<scalar>> $criteria
     */
    public function countBy(array $criteria = []): int;

    /**
     * @param array<string, mixed> $criteria
     * @param array<string, mixed>|null $orderBy
     *
     * @return \Ibexa\ProductCatalog\Local\Persistence\Values\AbstractProductPrice[]
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, int $offset = 0): array;

    /**
     * @param array<string, mixed> $criteria
     * @param array<string, mixed>|null $orderBy
     */
    public function findOneBy(array $criteria, ?array $orderBy = null): ?AbstractProductPrice;

    /**
     * @param array<string, mixed> $criteria
     */
    public function findOneByProductCode(
        string $productCode,
        int $currencyId,
        string $discriminator,
        array $criteria = []
    ): ?AbstractProductPrice;

    /**
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException
     */
    public function find(int $id): AbstractProductPrice;

    public function exists(int $id): bool;

    public function updateProductCode(string $newProductCode, string $oldProductCode): void;
}
