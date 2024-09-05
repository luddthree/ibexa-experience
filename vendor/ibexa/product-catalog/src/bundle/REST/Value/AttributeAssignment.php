<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionAssignmentInterface;
use Ibexa\Rest\Value;

final class AttributeAssignment extends Value
{
    public AttributeDefinitionAssignmentInterface $attributeDefinitionAssignment;

    public function __construct(AttributeDefinitionAssignmentInterface $attributeDefinitionAssignment)
    {
        $this->attributeDefinitionAssignment = $attributeDefinitionAssignment;
    }
}
