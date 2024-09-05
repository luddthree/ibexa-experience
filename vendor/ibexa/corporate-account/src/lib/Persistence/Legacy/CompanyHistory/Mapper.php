<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Persistence\Legacy\CompanyHistory;

use Ibexa\CorporateAccount\Persistence\Values\CompanyHistory;

/**
 * @phpstan-type Data = array{
 *     id: int,
 *     application_id: int,
 *     company_id: ?int,
 *     user_id: ?int,
 *     created: \DateTimeImmutable,
 *     event_name: string,
 *     event_data: null|array<string, mixed>
 * }
 */
final class Mapper
{
    /**
     * @phpstan-param Data $row
     */
    public function createFromRow(array $row): CompanyHistory
    {
        return new CompanyHistory(
            $row['id'],
            $row['application_id'],
            $row['company_id'],
            $row['user_id'],
            $row['event_name'],
            $row['created'],
            $row['event_data']
        );
    }

    /**
     * @phpstan-param array<Data> $rows
     *
     * @return \Ibexa\CorporateAccount\Persistence\Values\CompanyHistory[]
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
