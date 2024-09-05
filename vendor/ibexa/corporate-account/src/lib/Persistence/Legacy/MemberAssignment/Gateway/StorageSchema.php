<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Persistence\Legacy\MemberAssignment\Gateway;

final class StorageSchema
{
    public const TABLE_NAME = 'ibexa_corporate_member_assignment';

    public const COLUMN_ID = 'id';
    public const COLUMN_MEMBER_ID = 'member_id';
    public const COLUMN_MEMBER_ROLE = 'member_role';
    public const COLUMN_MEMBER_ROLE_ASSIGNMENT_ID = 'member_role_assignment_id';
    public const COLUMN_COMPANY_ID = 'company_id';
    public const COLUMN_COMPANY_LOCATION_ID = 'company_location_id';
}
