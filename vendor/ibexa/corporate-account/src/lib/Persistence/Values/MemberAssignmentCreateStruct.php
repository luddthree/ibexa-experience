<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Persistence\Values;

final class MemberAssignmentCreateStruct
{
    public int $memberId;

    public string $memberRole;

    public int $memberRoleAssignmentId;

    public int $companyId;

    public int $companyLocationId;

    public function __construct(
        int $memberId,
        string $memberRole,
        int $memberRoleAssignmentId,
        int $companyId,
        int $companyLocationId
    ) {
        $this->memberId = $memberId;
        $this->memberRole = $memberRole;
        $this->memberRoleAssignmentId = $memberRoleAssignmentId;
        $this->companyId = $companyId;
        $this->companyLocationId = $companyLocationId;
    }
}
