<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\Gateway;

final class StorageSchema
{
    public const TABLE_NAME = 'ibexa_product_specification_price';

    public const COLUMN_ID = 'id';
    public const COLUMN_PRODUCT_CODE = 'product_code';
    public const COLUMN_AMOUNT = 'amount';
    public const COLUMN_CURRENCY_ID = 'currency_id';
    public const COLUMN_DISCR = 'discriminator';
    public const COLUMN_CUSTOM_PRICE = 'custom_price_amount';
    public const COLUMN_CUSTOM_PRICE_RULE = 'custom_price_rule';
}
