<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Asset;

use Ibexa\Contracts\CorePersistence\Gateway\GatewayInterface as CoreGatewayInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\AssetCreateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Values\AssetUpdateStruct;

/**
 * @phpstan-type Data array{
 *     id: int,
 *     product_specification_id: int,
 *     uri: non-empty-string,
 *     tags: string[]
 * }
 *
 * @extends \Ibexa\Contracts\CorePersistence\Gateway\GatewayInterface<Data>
 */
interface GatewayInterface extends CoreGatewayInterface
{
    /**
     * @phpstan-return Data[]
     */
    public function findByProduct(int $productSpecificationId): array;

    public function create(AssetCreateStruct $createStruct): int;

    public function update(AssetUpdateStruct $updateStruct): void;

    public function delete(int $id): void;
}
