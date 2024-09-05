<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Converter;

use Ibexa\Bundle\Core\Converter\RepositoryParamConverter;
use Ibexa\Contracts\ProductCatalog\AttributeTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeTypeInterface;

final class AttributeTypeParamConverter extends RepositoryParamConverter
{
    private AttributeTypeServiceInterface $attributeTypeRegistry;

    public function __construct(AttributeTypeServiceInterface $attributeTypeRegistry)
    {
        $this->attributeTypeRegistry = $attributeTypeRegistry;
    }

    /**
     * @phpstan-return class-string
     */
    protected function getSupportedClass(): string
    {
        return AttributeTypeInterface::class;
    }

    protected function getPropertyName(): string
    {
        return 'attributeType';
    }

    /**
     * @param string $id
     */
    protected function loadValueObject($id): AttributeTypeInterface
    {
        return $this->attributeTypeRegistry->getAttributeType($id);
    }
}
