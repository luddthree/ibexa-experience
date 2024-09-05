<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local\Attribute;

use Ibexa\ProductCatalog\Local\Persistence\Values\Attribute;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeDefinition;

/**
 * @template TValue
 */
interface IndexDataProviderInterface
{
    /**
     * @param \Ibexa\ProductCatalog\Local\Persistence\Values\Attribute<TValue|null> $attribute
     *
     * @return iterable<\Ibexa\Contracts\Core\Search\Field>
     */
    public function getFieldsForAttribute(AttributeDefinition $attributeDefinition, Attribute $attribute): iterable;
}
