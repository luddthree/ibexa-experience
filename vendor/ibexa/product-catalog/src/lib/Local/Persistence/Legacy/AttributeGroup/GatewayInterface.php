<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeGroup;

use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeGroupCreateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeGroupUpdateStruct;

interface GatewayInterface
{
    /**
     * @phpstan-return array{id: int, identifier: string, position: int}>|null
     */
    public function findById(int $id): ?array;

    /**
     * @phpstan-return array{id: int, identifier: string, position: int}>|null
     */
    public function findByIdentifier(string $identifier): ?array;

    /**
     * @phpstan-return array<int,array{id: int, identifier: string, position: int}>>
     */
    public function findMatching(?string $namePrefix, int $offset, int $limit): array;

    public function countMatching(?string $namePrefix): int;

    public function insert(AttributeGroupCreateStruct $createStruct): int;

    public function deleteByIdentifier(string $identifier): void;

    public function update(AttributeGroupUpdateStruct $updateStruct): void;
}
