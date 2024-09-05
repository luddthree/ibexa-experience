<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog;

use Ibexa\Contracts\ProductCatalog\Values\AttributeTypeInterface;

interface AttributeTypeServiceInterface
{
    /**
     * @return iterable<string,\Ibexa\Contracts\ProductCatalog\Values\AttributeTypeInterface>
     */
    public function getAttributeTypes(): iterable;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function getAttributeType(string $identifier): AttributeTypeInterface;

    public function hasAttributeType(string $identifier): bool;
}
