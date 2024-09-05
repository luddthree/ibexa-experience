<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Catalog;

use Ibexa\ProductCatalog\Local\Persistence\Values\Catalog;

final class Mapper
{
    /**
     * @phpstan-param array{
     *     id: int,
     *     identifier: string,
     *     creator_id: int,
     *     created: int,
     *     modified: int,
     *     status: string,
     *     query_string: string,
     * } $row
     * @phpstan-param array<array{
     *     id: int,
     *     catalog_id: int,
     *     language_id: int,
     *     name: string,
     *     description: string,
     * }> $translations
     */
    public function createFromRow(array $row, array $translations): Catalog
    {
        $catalog = new Catalog();
        $catalog->id = $row['id'];
        $catalog->identifier = $row['identifier'];
        $catalog->creatorId = $row['creator_id'];
        $catalog->created = $row['created'];
        $catalog->modified = $row['modified'];
        $catalog->status = $row['status'];
        $catalog->query = $row['query_string'];

        foreach ($translations as $translation) {
            if ($translation['catalog_id'] !== $row['id']) {
                continue;
            }

            $languageId = $translation['language_id'];
            $catalog->setName($languageId, $translation['name']);
            $catalog->setDescription($languageId, $translation['description']);
        }

        return $catalog;
    }

    /**
     * @phpstan-param array<array{
     *     id: int,
     *     identifier: string,
     *     creator_id: int,
     *     created: int,
     *     modified: int,
     *     status: string,
     *     query_string: string,
     * }> $rows
     * @phpstan-param array<array{
     *     id: int,
     *     catalog_id: int,
     *     language_id: int,
     *     name: string,
     *     description: string,
     * }> $translations
     *
     * @return \Ibexa\ProductCatalog\Local\Persistence\Values\Catalog[]
     */
    public function createFromRows(array $rows, array $translations): array
    {
        $result = [];
        foreach ($rows as $row) {
            $result[] = $this->createFromRow($row, $translations);
        }

        return $result;
    }
}
