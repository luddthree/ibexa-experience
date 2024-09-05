<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice;

use Ibexa\Contracts\ProductCatalog\Values\Price\Create\Struct\ProductPriceCreateStructInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\Update\Struct\ProductPriceUpdateStructInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\GatewayInterface as BaseGatewayInterface;

/**
 * @phpstan-type Data array{
 *    id: int,
 *    amount: numeric-string,
 *    custom_price_amount: numeric-string|null,
 *    custom_price_rule: numeric-string|null,
 *    currency_id: int,
 *    product_code: non-empty-string,
 *    discriminator: string,
 * }
 *
 * @extends \Ibexa\ProductCatalog\Local\Persistence\Legacy\GatewayInterface<Data>
 */
interface GatewayInterface extends BaseGatewayInterface
{
    public function insert(ProductPriceCreateStructInterface $createStruct): int;

    /**
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException
     */
    public function update(ProductPriceUpdateStructInterface $updateStruct): void;

    /**
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException
     */
    public function delete(int $id): void;

    /**
     * @param \Doctrine\Common\Collections\Expr\Expression|array<string, \Doctrine\Common\Collections\Expr\Expression|scalar|array<scalar>> $criteria
     */
    public function countBy($criteria): int;

    /**
     * @phpstan-return Data|null
     */
    public function findById(int $id): ?array;

    /**
     * @param array<string, mixed> $criteria
     *
     * @phpstan-return array<Data>
     */
    public function findBy($criteria, ?array $orderBy = null, ?int $limit = null, int $offset = 0): array;

    /**
     * @param array<string, mixed> $criteria
     *
     * @phpstan-return Data|null
     */
    public function findOneByProductCode(
        string $productCode,
        int $currencyId,
        string $discriminator,
        array $criteria
    ): ?array;

    public function updateProductCode(string $newProductCode, string $oldProductCode): void;
}
