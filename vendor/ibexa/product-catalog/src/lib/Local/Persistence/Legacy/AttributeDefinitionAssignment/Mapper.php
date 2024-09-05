<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeDefinitionAssignment;

use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeDefinitionAssignment;

final class Mapper
{
    /**
     * @phpstan-param array<array-key,array{
     *      id: int,
     *      field_definition_id: int,
     *      attribute_definition_id: int,
     *      required: bool,
     *      discriminator: bool,
     * }> $rows
     *
     * @return \Ibexa\ProductCatalog\Local\Persistence\Values\AttributeDefinitionAssignment[]
     */
    public function extractFromRows(array $rows): array
    {
        $values = [];
        foreach ($rows as $row) {
            $values[] = $this->extractFromRow($row);
        }

        return $values;
    }

    /**
     * @phpstan-param array{
     *      id: int,
     *      field_definition_id: int,
     *      attribute_definition_id: int,
     *      required: bool,
     *      discriminator: bool,
     * } $row
     */
    public function extractFromRow(array $row): AttributeDefinitionAssignment
    {
        $value = new AttributeDefinitionAssignment();
        $value->id = (int)$row['id'];
        $value->fieldDefinitionId = (int)$row['field_definition_id'];
        $value->attributeDefinitionId = (int)$row['attribute_definition_id'];
        $value->required = (bool)$row['required'];
        $value->discriminator = (bool)$row['discriminator'];

        return $value;
    }
}
