<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Attribute;

use Ibexa\Contracts\ProductCatalog\Local\Attribute\VariantFormMapperInterface;
use Ibexa\ProductCatalog\Common\Pool;

final class VariantFormMapperRegistry implements VariantFormMapperRegistryInterface
{
    /** @var \Ibexa\ProductCatalog\Common\Pool<\Ibexa\Contracts\ProductCatalog\Local\Attribute\VariantFormMapperInterface> */
    private Pool $pool;

    /**
     * @param iterable<\Ibexa\Contracts\ProductCatalog\Local\Attribute\VariantFormMapperInterface> $mappers
     */
    public function __construct(iterable $mappers)
    {
        $this->pool = new Pool(VariantFormMapperRegistryInterface::class, $mappers);
        $this->pool->setExceptionArgumentName('type');
        $this->pool->setExceptionMessageTemplate('Could not find %s for \'%s\' attribute type');
    }

    public function hasMapper(string $type): bool
    {
        return $this->pool->has($type);
    }

    public function getMapper(string $type): VariantFormMapperInterface
    {
        return $this->pool->get($type);
    }
}
