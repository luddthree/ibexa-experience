<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Serializer;

final class DefaultAttributeValueMappingFactory implements AttributeValueMappingFactoryInterface
{
    public function getMapping(): array
    {
        return [
            'checkbox' => 'bool',
            'color' => 'string',
            'selection' => 'string',
            'integer' => 'int',
            'float' => 'float',
        ];
    }
}
