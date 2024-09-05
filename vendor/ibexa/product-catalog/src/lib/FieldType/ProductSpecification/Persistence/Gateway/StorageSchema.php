<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\FieldType\ProductSpecification\Persistence\Gateway;

final class StorageSchema
{
    public const TABLE_NAME = 'ibexa_product_specification';

    public const COLUMN_ID = 'id';
    public const COLUMN_CONTENT_ID = 'content_id';
    public const COLUMN_PRODUCT_ID = 'product_id';
    public const COLUMN_VERSION_NO = 'version_no';
    public const COLUMN_FIELD_ID = 'field_id';
    public const COLUMN_CODE = 'code';
    public const COLUMN_BASE_PRODUCT_ID = 'base_product_id';

    /**
     * @return string[]
     */
    public static function getColumns(): array
    {
        return [
            self::COLUMN_ID,
            self::COLUMN_CONTENT_ID,
            self::COLUMN_PRODUCT_ID,
            self::COLUMN_VERSION_NO,
            self::COLUMN_FIELD_ID,
            self::COLUMN_CODE,
            self::COLUMN_BASE_PRODUCT_ID,
        ];
    }
}
