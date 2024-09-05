<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeGroup;

use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeGroup;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeGroupCreateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeGroupUpdateStruct;

interface HandlerInterface
{
    public function load(int $id): AttributeGroup;

    public function loadByIdentifier(string $identifier): AttributeGroup;

    /**
     * @return \Ibexa\ProductCatalog\Local\Persistence\Values\AttributeGroup[]
     */
    public function findMatching(?string $namePrefix, int $offset, int $limit): array;

    public function countMatching(?string $namePrefix): int;

    public function create(AttributeGroupCreateStruct $createStruct): void;

    public function update(AttributeGroupUpdateStruct $updateStruct): void;

    public function deleteByIdentifier(string $identifier): void;

    public function deleteTranslation(string $identifier, int $languageId): void;
}
