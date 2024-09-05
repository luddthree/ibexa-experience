<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Permission\Policy\AttributeGroup;

use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;

final class PreEdit extends AbstractAttributeGroupPolicy
{
    private const EDIT = 'edit';

    private ?AttributeGroupInterface $attributeGroup;

    public function __construct(?AttributeGroupInterface $attributeGroup = null)
    {
        $this->attributeGroup = $attributeGroup;
    }

    public function getFunction(): string
    {
        return self::EDIT;
    }

    public function getObject(): ?AttributeGroupInterface
    {
        return $this->attributeGroup;
    }
}
