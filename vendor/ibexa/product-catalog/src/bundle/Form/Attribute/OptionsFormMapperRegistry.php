<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Attribute;

use Ibexa\Contracts\ProductCatalog\Local\Attribute\OptionsFormMapperInterface;
use Ibexa\ProductCatalog\Common\Pool;

final class OptionsFormMapperRegistry implements OptionsFormMapperRegistryInterface
{
    /** @var \Ibexa\ProductCatalog\Common\Pool<\Ibexa\Contracts\ProductCatalog\Local\Attribute\OptionsFormMapperInterface> */
    private Pool $pool;

    /**
     * @param iterable<\Ibexa\Contracts\ProductCatalog\Local\Attribute\OptionsFormMapperInterface> $mappers
     */
    public function __construct(iterable $mappers)
    {
        $this->pool = new Pool(OptionsFormMapperInterface::class, $mappers);
        $this->pool->setExceptionArgumentName('$type');
    }

    public function hasMapper(string $type): bool
    {
        return $this->pool->has($type);
    }

    public function getMapper(string $type): OptionsFormMapperInterface
    {
        return $this->pool->get($type);
    }
}
