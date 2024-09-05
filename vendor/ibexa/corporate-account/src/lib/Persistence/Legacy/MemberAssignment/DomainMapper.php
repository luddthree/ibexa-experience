<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Persistence\Legacy\MemberAssignment;

use Ibexa\CorporateAccount\Persistence\Values\MemberAssignment as PersistenceMemberAssignment;
use Ibexa\CorporateAccount\Values\MemberAssignment;

final class DomainMapper implements DomainMapperInterface
{
    public function createFromPersistence(PersistenceMemberAssignment $persistenceMemberAssignment): MemberAssignment
    {
        return new MemberAssignment(
            $persistenceMemberAssignment->id,
            $persistenceMemberAssignment->memberId,
            $persistenceMemberAssignment->memberRole,
            $persistenceMemberAssignment->memberRoleAssignmentId,
            $persistenceMemberAssignment->companyId,
            $persistenceMemberAssignment->companyLocationId,
        );
    }
}
