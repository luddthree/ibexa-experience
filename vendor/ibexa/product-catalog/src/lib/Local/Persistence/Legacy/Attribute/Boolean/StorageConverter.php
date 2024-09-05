<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Attribute\Boolean;

use Ibexa\Contracts\ProductCatalog\Local\Attribute\StorageConverterInterface;
use Webmozart\Assert\Assert;

/**
 * @implements \Ibexa\Contracts\ProductCatalog\Local\Attribute\StorageConverterInterface<
 *     array{'value': bool|null},
 *     bool,
 * >
 */
final class StorageConverter implements StorageConverterInterface
{
    public function fromPersistence(array $data)
    {
        $value = $data[StorageSchema::COLUMN_VALUE];
        Assert::nullOrBoolean($value);

        return $value;
    }

    public function toPersistence($value): array
    {
        Assert::nullOrBoolean($value);

        return [
            StorageSchema::COLUMN_VALUE => $value,
        ];
    }
}
