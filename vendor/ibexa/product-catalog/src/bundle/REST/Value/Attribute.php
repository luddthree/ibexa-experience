<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Ibexa\Rest\Value;

final class Attribute extends Value
{
    public AttributeDefinitionInterface $attributeDefinition;

    /** @var mixed|null */
    public $value;

    /**
     * @param mixed $value
     */
    public function __construct(AttributeDefinitionInterface $attributeDefinition, $value = null)
    {
        $this->attributeDefinition = $attributeDefinition;
        $this->value = $value;
    }
}
