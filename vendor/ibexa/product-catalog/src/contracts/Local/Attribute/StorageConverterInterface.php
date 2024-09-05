<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local\Attribute;

/**
 * @template TPersistenceValue of array<string, mixed>
 * @template TValue
 */
interface StorageConverterInterface
{
    /**
     * @param TPersistenceValue $data
     *
     * @return TValue|null
     */
    public function fromPersistence(array $data);

    /**
     * @param TValue|null $value
     *
     * @return TPersistenceValue
     */
    public function toPersistence($value): array;
}
