<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Converter;

use Ibexa\Bundle\Core\Converter\RepositoryParamConverter;
use Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;

final class AttributeDefinitionParamConverter extends RepositoryParamConverter
{
    private AttributeDefinitionServiceInterface $attributeDefinitionService;

    public function __construct(AttributeDefinitionServiceInterface $attributeDefinitionService)
    {
        $this->attributeDefinitionService = $attributeDefinitionService;
    }

    /**
     * @phpstan-return class-string
     */
    protected function getSupportedClass(): string
    {
        return AttributeDefinitionInterface::class;
    }

    protected function getPropertyName(): string
    {
        return 'attributeDefinitionIdentifier';
    }

    /**
     * @param string $id
     */
    protected function loadValueObject($id): AttributeDefinitionInterface
    {
        return $this->attributeDefinitionService->getAttributeDefinition($id);
    }
}
