<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeGroup;

use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeGroup;

final class Mapper
{
    /**
     * @phpstan-param array<array{
     *     id: int,
     *     identifier: string,
     *     position: int
     * }> $rows
     * @phpstan-param array<array{
     *     id: int,
     *     attribute_group_id: int,
     *     language_id: int,
     *     name: string
     * }> $translations
     *
     * @return \Ibexa\ProductCatalog\Local\Persistence\Values\AttributeGroup[]
     */
    public function extractFromRows(array $rows, array $translations = []): array
    {
        $values = [];
        foreach ($rows as $row) {
            $value = new AttributeGroup();
            $value->id = (int)$row['id'];
            $value->identifier = $row['identifier'];
            $value->position = (int)$row['position'];

            foreach ($translations as $translation) {
                if ($translation['attribute_group_id'] === $row['id']) {
                    $value->names[(int)$translation['language_id']] = $translation['name'];
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
     *     position: int
     * } $row
     * @phpstan-param array<array{
     *     id: int,
     *     attribute_group_id: int,
     *     language_id: int,
     *     name: string
     * }> $translations
     */
    public function extractFromRow(array $row, array $translations): AttributeGroup
    {
        $value = new AttributeGroup();
        $value->id = (int)$row['id'];
        $value->identifier = $row['identifier'];
        $value->position = (int)$row['position'];
        foreach ($translations as $translation) {
            $value->names[(int)$translation['language_id']] = $translation['name'];
        }

        return $value;
    }
}
