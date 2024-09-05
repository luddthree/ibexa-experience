<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Attribute;

use Ibexa\Contracts\CorePersistence\Gateway\GatewayInterface as CoreGatewayInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeCreateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeDefinition;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeUpdateStruct;

/**
 * @phpstan-type Data array{
 *   id: int,
 *   product_specification_id: int,
 *   attribute_definition_id: int,
 *   discriminator: string,
 * }
 *
 * @extends \Ibexa\Contracts\CorePersistence\Gateway\GatewayInterface<Data>
 */
interface GatewayInterface extends CoreGatewayInterface
{
    /**
     * @return array<Data>
     */
    public function findByProduct(int $productSpecificationId): array;

    public function create(AttributeCreateStruct $struct, AttributeDefinition $attributeDefinition): int;

    public function deleteByProduct(int $productSpecificationId): void;

    public function countProductsContainingAttribute(int $id): int;

    public function countProductsContainingAttributeGroup(int $id): int;

    public function update(AttributeUpdateStruct $updateStruct): void;

    public function deleteByAttributeDefinition(int $attributeDefinitionId): void;
}
