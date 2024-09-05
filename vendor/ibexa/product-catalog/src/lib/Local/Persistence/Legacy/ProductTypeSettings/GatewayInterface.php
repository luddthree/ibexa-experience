<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductTypeSettings;

use Ibexa\Contracts\CorePersistence\Gateway\GatewayInterface as CoreGatewayInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\ProductTypeSettingCreateStruct;

/**
 * @internal
 *
 * @phpstan-type Data array{
 *     id: int,
 *     field_definition_id: int,
 *     status: int,
 *     is_virtual: bool,
 * }
 *
 * @extends \Ibexa\Contracts\CorePersistence\Gateway\GatewayInterface<Data>
 */
interface GatewayInterface extends CoreGatewayInterface
{
    public function insert(ProductTypeSettingCreateStruct $createStruct): int;

    /**
     * @param array<string, mixed> $criteria
     */
    public function deleteBy(array $criteria): void;

    /**
     * @param array<string, mixed> $data
     */
    public function update(array $data, int $fieldDefinitionId, int $status): void;
}
