<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\CustomerGroup;

use Ibexa\ProductCatalog\Local\Persistence\Values\CustomerGroup;

final class Mapper
{
    /**
     * @phpstan-param array{
     *   id: int,
     *   identifier: string,
     *   global_price_rate: numeric-string,
     * } $row
     * @phpstan-param array<array{
     *     id: int,
     *     customer_group_id: int,
     *     language_id: int,
     *     name: string,
     *     description: string,
     * }> $translations
     */
    public function createFromRow(array $row, array $translations): CustomerGroup
    {
        $customerGroup = new CustomerGroup();

        $customerGroup->id = $row['id'];
        $customerGroup->identifier = $row['identifier'];
        $customerGroup->globalPriceRate = $row['global_price_rate'];

        foreach ($translations as $translation) {
            if ($translation['customer_group_id'] !== $row['id']) {
                continue;
            }

            $languageId = $translation['language_id'];
            $customerGroup->setName($languageId, $translation['name']);
            $customerGroup->setDescription($languageId, $translation['description']);
        }

        return $customerGroup;
    }

    /**
     * @phpstan-param array<array{
     *   id: int,
     *   identifier: string,
     *   global_price_rate: numeric-string,
     * }> $rows
     * @phpstan-param array<array{
     *     id: int,
     *     customer_group_id: int,
     *     language_id: int,
     *     name: string,
     *     description: string,
     * }> $translations
     *
     * @return \Ibexa\ProductCatalog\Local\Persistence\Values\CustomerGroup[]
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
