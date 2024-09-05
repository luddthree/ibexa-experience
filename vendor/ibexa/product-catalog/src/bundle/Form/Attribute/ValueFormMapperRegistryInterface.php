<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Attribute;

use Ibexa\Contracts\ProductCatalog\Local\Attribute\ValueFormMapperInterface;

interface ValueFormMapperRegistryInterface
{
    public function hasMapper(string $type): bool;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function getMapper(string $type): ValueFormMapperInterface;
}
