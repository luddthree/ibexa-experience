<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeDefinitionAssignment;

use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeDefinitionAssignmentCreateStruct;

interface GatewayInterface
{
    /**
     * @phpstan-return array<array-key,array{
     *      id: int,
     *      field_definition_id: int,
     *      attribute_definition_id: int,
     *      required: bool,
     *      discriminator: bool
     * }>
     */
    public function findByFieldDefinitionId(int $fieldDefinitionId, int $status): array;

    public function insert(AttributeDefinitionAssignmentCreateStruct $createStruct, int $status): int;

    public function publish(int $fieldDefinitionId): void;

    public function deleteByFieldDefinitionId(int $fieldDefinitionId, int $status): void;

    /**
     * @return array<int,string>|array<string,int>
     */
    public function getIdentityMap(int $fieldDefinitionId, bool $discriminator): array;
}
