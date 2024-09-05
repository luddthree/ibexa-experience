<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Asset;

use Ibexa\ProductCatalog\Local\Persistence\Values\Asset;
use Ibexa\ProductCatalog\Local\Persistence\Values\AssetCreateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Values\AssetUpdateStruct;

interface HandlerInterface
{
    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function load(int $id): Asset;

    /**
     * @return \Ibexa\ProductCatalog\Local\Persistence\Values\Asset[]
     */
    public function findByProduct(int $productSpecificationId): array;

    public function create(AssetCreateStruct $createStruct): Asset;

    public function update(AssetUpdateStruct $updateStruct): void;

    public function delete(int $id): void;
}
