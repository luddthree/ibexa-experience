<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Permission\Policy\AttributeDefinition;

use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;

final class Delete extends AbstractAttributeDefinitionPolicy
{
    private const DELETE = 'delete';

    private ?AttributeDefinitionInterface $attributeDefinition;

    public function __construct(?AttributeDefinitionInterface $attributeDefinition = null)
    {
        $this->attributeDefinition = $attributeDefinition;
    }

    public function getFunction(): string
    {
        return self::DELETE;
    }

    public function getObject(): ?AttributeDefinitionInterface
    {
        return $this->attributeDefinition;
    }
}
