<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Dispatcher;

use Ibexa\Contracts\ProductCatalog\AttributeGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroup\AttributeGroupListInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroup\AttributeGroupQuery;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;

/**
 * @extends \Ibexa\ProductCatalog\Dispatcher\AbstractServiceDispatcher<
 *     \Ibexa\Contracts\ProductCatalog\AttributeGroupServiceInterface
 * >
 */
final class AttributeGroupServiceDispatcher extends AbstractServiceDispatcher implements AttributeGroupServiceInterface
{
    /**
     * @param iterable<\Ibexa\Contracts\Core\Repository\Values\Content\Language>|null $prioritizedLanguages
     */
    public function getAttributeGroup(string $identifier, ?iterable $prioritizedLanguages = null): AttributeGroupInterface
    {
        return $this->dispatch()->getAttributeGroup($identifier, $prioritizedLanguages);
    }

    public function findAttributeGroups(?AttributeGroupQuery $query = null): AttributeGroupListInterface
    {
        return $this->dispatch()->findAttributeGroups($query);
    }
}
