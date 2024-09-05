<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeDefinition;

use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeDefinition;

final class Mapper
{
    /**
     * @phpstan-param array<array{
     *     id: int,
     *     identifier: string,
     *     type: string,
     *     attribute_group_id: int,
     *     position: int,
     *     options: ?array<string,mixed>,
     * }> $rows
     * @phpstan-param array<array{
     *     id: int,
     *     attribute_definition_id: int,
     *     language_id: int,
     *     name: string,
     *     description: ?string,
     * }> $translations
     *
     * @return \Ibexa\ProductCatalog\Local\Persistence\Values\AttributeDefinition[]
     */
    public function extractFromRows(array $rows, array $translations = []): array
    {
        $values = [];
        foreach ($rows as $row) {
            $value = new AttributeDefinition();
            $value->id = (int)$row['id'];
            $value->identifier = $row['identifier'];
            $value->type = $row['type'];
            $value->attributeGroupId = (int)$row['attribute_group_id'];
            $value->position = (int)$row['position'];
            $value->options = $row['options'] ?? [];

            foreach ($translations as $translation) {
                if ($translation['attribute_definition_id'] === $row['id']) {
                    $languageId = (int)$translation['language_id'];

                    $value->setName($languageId, $translation['name']);
                    $value->setDescription($languageId, $translation['description']);
                }
            }

            $values[] = $value;
        }

        return $values;
    }

    /**
     * @phpstan-param array{
     *     id: int,
     *     identifier: string,
     *     type: string,
     *     attribute_group_id: int,
     *     position: int,
     *     options: ?array<string,mixed>,
     * } $row
     * @phpstan-param array<array{
     *     id: int,
     *     attribute_definition_id: int,
     *     language_id: int,
     *     name: string,
     *     description: ?string
     * }> $translations
     */
    public function extractFromRow(array $row, array $translations): AttributeDefinition
    {
        $value = new AttributeDefinition();
        $value->id = (int)$row['id'];
        $value->identifier = $row['identifier'];
        $value->type = $row['type'];
        $value->attributeGroupId = (int)$row['attribute_group_id'];
        $value->position = (int)$row['position'];
        $value->options = $row['options'] ?? [];

        foreach ($translations as $translation) {
            $languageId = (int)$translation['language_id'];
            $value->setName($languageId, $translation['name']);
            $value->setDescription($languageId, $translation['description']);
        }

        return $value;
    }
}
