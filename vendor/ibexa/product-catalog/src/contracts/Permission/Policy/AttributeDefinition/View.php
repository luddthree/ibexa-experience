<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Permission\Policy\AttributeDefinition;

use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;

final class View extends AbstractAttributeDefinitionPolicy
{
    private const VIEW = 'view';

    private ?AttributeDefinitionInterface $attributeDefinition;

    public function __construct(?AttributeDefinitionInterface $attributeDefinition = null)
    {
        $this->attributeDefinition = $attributeDefinition;
    }

    public function getFunction(): string
    {
        return self::VIEW;
    }

    public function getObject(): ?AttributeDefinitionInterface
    {
        return $this->attributeDefinition;
    }
}
