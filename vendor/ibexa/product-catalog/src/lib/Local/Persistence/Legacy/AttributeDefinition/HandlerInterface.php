<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeDefinition;

use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\AttributeDefinitionQuery;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeDefinition;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeDefinitionCreateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeDefinitionUpdateStruct;

interface HandlerInterface
{
    public function load(int $id): AttributeDefinition;

    public function loadByIdentifier(string $identifier): AttributeDefinition;

    /**
     * @return \Ibexa\ProductCatalog\Local\Persistence\Values\AttributeDefinition[]
     */
    public function findMatching(AttributeDefinitionQuery $query): array;

    public function countMatching(AttributeDefinitionQuery $query): int;

    public function create(AttributeDefinitionCreateStruct $createStruct): void;

    public function update(AttributeDefinitionUpdateStruct $updateStruct): void;

    public function deleteByIdentifier(string $identifier): void;

    public function deleteTranslation(string $identifier, int $languageId): void;
}
