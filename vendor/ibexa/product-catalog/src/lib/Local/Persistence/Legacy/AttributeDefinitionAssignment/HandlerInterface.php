<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeDefinitionAssignment;

use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeDefinitionAssignmentCreateStruct;

interface HandlerInterface
{
    public function create(AttributeDefinitionAssignmentCreateStruct $createStruct, int $status): void;

    public function publish(int $fieldDefinitionId): void;

    public function deleteByFieldDefinitionId(int $fieldDefinitionId, int $status): void;

    /**
     * @return \Ibexa\ProductCatalog\Local\Persistence\Values\AttributeDefinitionAssignment[]
     */
    public function findByFieldDefinitionId(int $fieldDefinitionId, int $status): array;

    /**
     * Returns attribute definition id to identifier map.
     *
     * @return array<int,string>|array<string,int>
     */
    public function getIdentityMap(int $fieldDefinitionId, bool $discriminator = false): array;
}
