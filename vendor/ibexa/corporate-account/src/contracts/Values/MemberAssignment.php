<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CorporateAccount\Values;

interface MemberAssignment
{
    public function getId(): int;

    public function getMemberId(): int;

    public function getMemberRole(): string;

    public function getMemberRoleAssignmentId(): int;

    public function getCompanyId(): int;

    public function getCompanyLocationId(): int;
}
