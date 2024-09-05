<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeDefinition\Gateway;

final class Schema
{
    public const TABLE_NAME = 'ibexa_attribute_definition';

    public const COLUMN_POSITION = 'position';
    public const COLUMN_TYPE = 'type';
    public const COLUMN_IDENTIFIER = 'identifier';
    public const COLUMN_ID = 'id';
    public const COLUMN_OPTIONS = 'options';
    public const COLUMN_ATTRIBUTE_GROUP_ID = 'attribute_group_id';
}
