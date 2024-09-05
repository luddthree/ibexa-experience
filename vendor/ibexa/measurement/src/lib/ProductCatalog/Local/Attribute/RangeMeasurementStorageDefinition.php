<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\ProductCatalog\Local\Attribute;

use Doctrine\DBAL\Types\Types;
use Ibexa\Contracts\ProductCatalog\Local\Attribute\StorageDefinitionInterface;

final class RangeMeasurementStorageDefinition implements StorageDefinitionInterface
{
    public const TABLE_NAME = 'ibexa_product_specification_attribute_measurement_range';

    public const COLUMN_MIN_VALUE = 'min_value';
    public const COLUMN_MAX_VALUE = 'max_value';
    public const COLUMN_UNIT_ID = 'unit_id';
    public const COLUMN_BASE_MIN_VALUE = 'base_min_value';
    public const COLUMN_BASE_MAX_VALUE = 'base_max_value';
    public const COLUMN_BASE_UNIT_ID = 'base_unit_id';

    public function getColumns(): array
    {
        return [
            self::COLUMN_MIN_VALUE => Types::FLOAT,
            self::COLUMN_MAX_VALUE => Types::FLOAT,
            self::COLUMN_UNIT_ID => Types::INTEGER,
            self::COLUMN_BASE_MIN_VALUE => Types::FLOAT,
            self::COLUMN_BASE_MAX_VALUE => Types::FLOAT,
            self::COLUMN_BASE_UNIT_ID => Types::INTEGER,
        ];
    }

    public function getTableName(): string
    {
        return self::TABLE_NAME;
    }
}
