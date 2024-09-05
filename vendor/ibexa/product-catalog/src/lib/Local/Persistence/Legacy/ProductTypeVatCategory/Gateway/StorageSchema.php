<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductTypeVatCategory\Gateway;

final class StorageSchema
{
    public const TABLE_NAME = 'ibexa_product_type_specification_region_vat_category';

    public const COLUMN_ID = 'id';
    public const COLUMN_FIELD_DEFINITION_ID = 'field_definition_id';
    public const COLUMN_STATUS = 'status';
    public const COLUMN_REGION = 'region';
    public const COLUMN_VAT_CATEGORY = 'vat_category';
}
