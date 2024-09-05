<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local\Attribute;

use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;

interface ValueValidatorInterface
{
    /**
     * @param mixed $value
     *
     * @return \Ibexa\Contracts\ProductCatalog\Local\Attribute\ValueValidationError[]
     */
    public function validateValue(AttributeDefinitionInterface $attributeDefinition, $value): iterable;
}
