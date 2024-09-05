<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Persistence\Legacy\MemberAssignment;

use Ibexa\CorporateAccount\Persistence\Values\MemberAssignment;

final class Mapper
{
    /**
     * @phpstan-param array{
     *     id: int,
     *     member_id: int,
     *     company_id: int,
     *     company_location_id: int,
     *     member_role: string,
     *     member_role_assignment_id: int
     * } $row
     */
    public function createFromRow(array $row): MemberAssignment
    {
        return new MemberAssignment(
            $row['id'],
            $row['member_id'],
            $row['member_role'],
            $row['member_role_assignment_id'],
            $row['company_id'],
            $row['company_location_id']
        );
    }

    /**
     * @phpstan-param array<array{
     *     id: int,
     *     member_id: int,
     *     company_id: int,
     *     company_location_id: int,
     *     member_role: string,
     *     member_role_assignment_id: int
     * }> $rows
     *
     * @return \Ibexa\CorporateAccount\Persistence\Values\MemberAssignment[]
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
