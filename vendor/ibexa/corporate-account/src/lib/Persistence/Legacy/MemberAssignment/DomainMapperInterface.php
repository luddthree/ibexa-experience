<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Persistence\Legacy\MemberAssignment;

use Ibexa\Contracts\CorporateAccount\Values\MemberAssignment;
use Ibexa\CorporateAccount\Persistence\Values\MemberAssignment as PersistenceMemberAssignment;

interface DomainMapperInterface
{
    public function createFromPersistence(PersistenceMemberAssignment $persistenceMemberAssignment): MemberAssignment;
}
