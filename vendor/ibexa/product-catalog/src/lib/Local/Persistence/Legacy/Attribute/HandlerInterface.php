<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Attribute;

use Ibexa\Contracts\CorePersistence\HandlerInterface as CoreHandlerInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeCreateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeUpdateStruct;

/**
 * @extends \Ibexa\Contracts\CorePersistence\HandlerInterface<
 *     \Ibexa\ProductCatalog\Local\Persistence\Values\Attribute<mixed>,
 * >
 */
interface HandlerInterface extends CoreHandlerInterface
{
    public function create(AttributeCreateStruct $struct): int;

    /**
     * @return \Ibexa\ProductCatalog\Local\Persistence\Values\Attribute<mixed>[]
     */
    public function findByProduct(int $productSpecificationId): array;

    public function getProductCount(int $id): int;

    public function getProductCountByAttributeGroup(int $id): int;

    public function update(AttributeUpdateStruct $updateStruct): void;
}
