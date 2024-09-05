<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Dispatcher;

use Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\AttributeDefinitionListInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\AttributeDefinitionQuery;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;

/**
 * @extends \Ibexa\ProductCatalog\Dispatcher\AbstractServiceDispatcher<
 *     \Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface
 * >
 */
final class AttributeDefinitionServiceDispatcher extends AbstractServiceDispatcher implements AttributeDefinitionServiceInterface
{
    /**
     * @param iterable<string>|null $prioritizedLanguages
     */
    public function getAttributeDefinition(string $identifier, ?iterable $prioritizedLanguages = null): AttributeDefinitionInterface
    {
        return $this->dispatch()->getAttributeDefinition($identifier, $prioritizedLanguages);
    }

    public function findAttributesDefinitions(?AttributeDefinitionQuery $query = null): AttributeDefinitionListInterface
    {
        return $this->dispatch()->findAttributesDefinitions($query);
    }
}
