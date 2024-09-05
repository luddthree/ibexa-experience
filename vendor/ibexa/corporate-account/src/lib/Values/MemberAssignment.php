<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Values;

use Ibexa\Contracts\CorporateAccount\Values\MemberAssignment as MemberAssignmentInterface;

final class MemberAssignment implements MemberAssignmentInterface
{
    public int $id;

    public int $memberId;

    public string $memberRole;

    public int $memberRoleAssignmentId;

    public int $companyId;

    public int $companyLocationId;

    public function __construct(
        int $id,
        int $memberId,
        string $memberRole,
        int $memberRoleAssignmentId,
        int $companyId,
        int $companyLocationId
    ) {
        $this->id = $id;
        $this->memberId = $memberId;
        $this->memberRole = $memberRole;
        $this->memberRoleAssignmentId = $memberRoleAssignmentId;
        $this->companyId = $companyId;
        $this->companyLocationId = $companyLocationId;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getMemberId(): int
    {
        return $this->memberId;
    }

    public function getMemberRole(): string
    {
        return $this->memberRole;
    }

    public function getMemberRoleAssignmentId(): int
    {
        return $this->memberRoleAssignmentId;
    }

    public function getCompanyId(): int
    {
        return $this->companyId;
    }

    public function getCompanyLocationId(): int
    {
        return $this->companyLocationId;
    }
}
