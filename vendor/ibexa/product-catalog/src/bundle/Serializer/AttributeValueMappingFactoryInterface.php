<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Serializer;

interface AttributeValueMappingFactoryInterface
{
    /**
     * @phpstan-return array<non-empty-string, class-string|"int"|"float"|"bool"|"string">
     */
    public function getMapping(): array;
}
