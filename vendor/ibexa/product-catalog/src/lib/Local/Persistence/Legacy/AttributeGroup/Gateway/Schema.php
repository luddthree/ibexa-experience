<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeGroup\Gateway;

/**
 * @internal
 */
final class Schema
{
    public const TABLE_NAME = 'ibexa_attribute_group';

    public const COLUMN_IDENTIFIER = 'identifier';
    public const COLUMN_ID = 'id';
    public const COLUMN_POSITION = 'position';
}
