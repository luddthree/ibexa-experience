<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Attribute\Selection;

use Doctrine\DBAL\Types\Types;
use Ibexa\Contracts\ProductCatalog\Local\Attribute\StorageDefinitionInterface;

final class StorageDefinition implements StorageDefinitionInterface
{
    public function getColumns(): array
    {
        return [
            StorageSchema::COLUMN_VALUE => Types::STRING,
        ];
    }

    public function getTableName(): string
    {
        return StorageSchema::TABLE_NAME;
    }
}
