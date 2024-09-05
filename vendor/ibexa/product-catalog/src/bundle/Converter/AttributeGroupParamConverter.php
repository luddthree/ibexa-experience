<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Converter;

use Ibexa\Bundle\Core\Converter\RepositoryParamConverter;
use Ibexa\Contracts\ProductCatalog\AttributeGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;

final class AttributeGroupParamConverter extends RepositoryParamConverter
{
    private AttributeGroupServiceInterface $attributeGroupService;

    public function __construct(AttributeGroupServiceInterface $attributeGroupService)
    {
        $this->attributeGroupService = $attributeGroupService;
    }

    /**
     * @phpstan-return class-string
     */
    protected function getSupportedClass(): string
    {
        return AttributeGroupInterface::class;
    }

    protected function getPropertyName(): string
    {
        return 'attributeGroupIdentifier';
    }

    /**
     * @param string $identifier
     */
    protected function loadValueObject($identifier): AttributeGroupInterface
    {
        return $this->attributeGroupService->getAttributeGroup($identifier);
    }
}
