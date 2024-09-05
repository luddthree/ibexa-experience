<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Persistence\Legacy\ApplicationState;

use Ibexa\CorporateAccount\Persistence\Values\ApplicationState;

/**
 * @phpstan-type Data = array{
 *     id: int,
 *     application_id: int,
 *     state: string,
 *     company_id: ?int
 * }
 */
final class Mapper
{
    /**
     * @phpstan-param Data $row
     */
    public function createFromRow(array $row): ApplicationState
    {
        return new ApplicationState(
            $row['id'],
            $row['application_id'],
            $row['state'],
            $row['company_id'] ?? null
        );
    }

    /**
     * @phpstan-param array<Data> $rows
     *
     * @return \Ibexa\CorporateAccount\Persistence\Values\ApplicationState[]
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
