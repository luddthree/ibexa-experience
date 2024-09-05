<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local\Attribute;

interface StorageDefinitionInterface
{
    /**
     * @phpstan-return array<non-empty-string, \Doctrine\DBAL\Types\Types::*>
     */
    public function getColumns(): array;

    /**
     * @phpstan-return non-empty-string
     */
    public function getTableName(): string;
}
