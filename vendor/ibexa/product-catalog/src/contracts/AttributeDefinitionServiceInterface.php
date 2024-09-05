<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog;

use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\AttributeDefinitionListInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\AttributeDefinitionQuery;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;

interface AttributeDefinitionServiceInterface
{
    /**
     * @param iterable<string>|null $prioritizedLanguages
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function getAttributeDefinition(
        string $identifier,
        ?iterable $prioritizedLanguages = null
    ): AttributeDefinitionInterface;

    public function findAttributesDefinitions(?AttributeDefinitionQuery $query = null): AttributeDefinitionListInterface;
}
