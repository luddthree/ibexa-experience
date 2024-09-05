<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Attribute\SimpleCustom;

use Ibexa\Contracts\ProductCatalog\Local\Attribute\StorageConverterInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\Attribute\Boolean\StorageSchema;

/**
 * @implements \Ibexa\Contracts\ProductCatalog\Local\Attribute\StorageConverterInterface<
 *     array{'value': mixed|null},
 *     array<scalar>,
 * >
 */
final class StorageConverter implements StorageConverterInterface
{
    public function fromPersistence(array $data)
    {
        return $data[StorageSchema::COLUMN_VALUE];
    }

    public function toPersistence($value): array
    {
        return [
            StorageSchema::COLUMN_VALUE => $value,
        ];
    }
}
