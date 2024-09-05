<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog;

use Ibexa\Contracts\ProductCatalog\Values\AttributeGroup\AttributeGroupListInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroup\AttributeGroupQuery;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;

interface AttributeGroupServiceInterface
{
    /**
     * @param iterable<\Ibexa\Contracts\Core\Repository\Values\Content\Language>|null $prioritizedLanguages
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function getAttributeGroup(string $identifier, ?iterable $prioritizedLanguages = null): AttributeGroupInterface;

    public function findAttributeGroups(?AttributeGroupQuery $query = null): AttributeGroupListInterface;
}
