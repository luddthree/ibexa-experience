<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\ProductCatalog\Form\Attribute\Search;

use Ibexa\Contracts\ProductCatalog\Local\Attribute\IndexDataProviderInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\Attribute;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeDefinition;

/**
 * @template-implements \Ibexa\Contracts\ProductCatalog\Local\Attribute\IndexDataProviderInterface<array<mixed>>
 */
final class IndexDataProvider implements IndexDataProviderInterface
{
    public function getFieldsForAttribute(AttributeDefinition $attributeDefinition, Attribute $attribute): iterable
    {
        return [
            // TODO: Actual getFieldsForAttribute implementation
        ];
    }
}
