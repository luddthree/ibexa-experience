<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Persistence\Legacy\ApplicationState\Gateway;

final class StorageSchema
{
    public const TABLE_NAME = 'ibexa_corporate_application_state';

    public const COLUMN_ID = 'id';
    public const COLUMN_APPLICATION_ID = 'application_id';
    public const COLUMN_STATE = 'state';
    public const COLUMN_COMPANY_ID = 'company_id';
}
