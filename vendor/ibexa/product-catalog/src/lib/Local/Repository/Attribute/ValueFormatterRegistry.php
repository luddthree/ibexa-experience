<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Attribute;

use Ibexa\Contracts\ProductCatalog\Local\Attribute\ValueFormatterInterface;
use Ibexa\ProductCatalog\Common\Pool;

final class ValueFormatterRegistry implements ValueFormatterRegistryInterface
{
    /** @var \Ibexa\ProductCatalog\Common\Pool<\Ibexa\Contracts\ProductCatalog\Local\Attribute\ValueFormatterInterface> */
    private Pool $pool;

    /**
     * @param iterable<string, \Ibexa\Contracts\ProductCatalog\Local\Attribute\ValueFormatterInterface> $formatters
     */
    public function __construct(iterable $formatters)
    {
        $this->pool = new Pool(ValueFormatterInterface::class, $formatters);
        $this->pool->setExceptionArgumentName('type');
        $this->pool->setExceptionMessageTemplate('Could not find %s for \'%s\' attribute type');
    }

    public function hasFormatter(string $type): bool
    {
        return $this->pool->has($type);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function getFormatter(string $type): ValueFormatterInterface
    {
        return $this->pool->get($type);
    }
}
