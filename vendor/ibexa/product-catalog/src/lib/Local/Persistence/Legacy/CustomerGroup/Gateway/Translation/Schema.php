<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\CustomerGroup\Gateway\Translation;

/**
 * @internal
 */
final class Schema
{
    public const TABLE_NAME = 'ibexa_customer_group_ml';

    public const COLUMN_ID = 'id';
    public const COLUMN_CUSTOMER_GROUP_ID = 'customer_group_id';
    public const COLUMN_LANGUAGE_ID = 'language_id';
    public const COLUMN_NAME = 'name';
    public const COLUMN_NAME_NORMALIZED = 'name_normalized';
    public const COLUMN_DESCRIPTION = 'description';
}
