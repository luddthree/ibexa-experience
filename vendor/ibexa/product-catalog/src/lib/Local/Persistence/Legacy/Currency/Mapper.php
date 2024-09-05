<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Currency;

use Ibexa\ProductCatalog\Local\Persistence\Values\Currency;

final class Mapper
{
    /**
     * @phpstan-param array{
     *     id: int,
     *     code: non-empty-string,
     *     subunits: int<0, max>,
     *     enabled: bool,
     * } $row
     */
    public function createFromRow(array $row): Currency
    {
        return new Currency(
            $row['id'],
            $row['code'],
            $row['subunits'],
            $row['enabled'],
        );
    }

    /**
     * @phpstan-param array<array{
     *     id: int,
     *     code: non-empty-string,
     *     subunits: int<0, max>,
     *     enabled: bool,
     * }> $rows
     *
     * @return \Ibexa\ProductCatalog\Local\Persistence\Values\Currency[]
     */
    public function createFromRows(array $rows): array
    {
        $result = [];
        foreach ($rows as $row) {
            $result[] = $this->createFromRow($row);
        }

        return $result;
    }
}
