<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeDefinition;

use Ibexa\Contracts\CorePersistence\Gateway\GatewayInterface as CoreGatewayInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeDefinitionCreateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeDefinitionUpdateStruct;

/**
 * @extends \Ibexa\Contracts\CorePersistence\Gateway\GatewayInterface<array{
 *     id: int,
 *     identifier: string,
 *     type: string,
 *     attribute_group_id: int,
 *     position: int,
 *     options: ?array<string,mixed>,
 * }>
 */
interface GatewayInterface extends CoreGatewayInterface
{
    /**
     * @phpstan-return array{
     *     id: int,
     *     identifier: string,
     *     type: string,
     *     attribute_group_id: int,
     *     position: int,
     *     options: ?array<string,mixed>,
     * }>|null
     */
    public function findById(int $id): ?array;

    /**
     * @phpstan-return array{
     *     id: int,
     *     identifier: string,
     *     type: string,
     *     attribute_group_id: int,
     *     position: int,
     *     options: ?array<string,mixed>,
     * }>|null
     */
    public function findByIdentifier(string $identifier): ?array;

    public function insert(AttributeDefinitionCreateStruct $createStruct): int;

    public function deleteByIdentifier(string $identifier): void;

    public function update(AttributeDefinitionUpdateStruct $updateStruct): void;
}
