<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Attribute;

use Ibexa\Contracts\ProductCatalog\Local\Attribute\ValueValidatorInterface;
use Ibexa\ProductCatalog\Common\Pool;

final class ValueValidatorRegistry implements ValueValidatorRegistryInterface
{
    /** @var \Ibexa\ProductCatalog\Common\Pool<\Ibexa\Contracts\ProductCatalog\Local\Attribute\ValueValidatorInterface> */
    private Pool $pool;

    /**
     * @param iterable<\Ibexa\Contracts\ProductCatalog\Local\Attribute\ValueValidatorInterface> $validators
     */
    public function __construct(iterable $validators = [])
    {
        $this->pool = new Pool(ValueValidatorInterface::class, $validators);
        $this->pool->setExceptionArgumentName('type');
        $this->pool->setExceptionMessageTemplate('Could not find %s for \'%s\' attribute type');
    }

    public function hasValidator(string $alias): bool
    {
        return $this->pool->has($alias);
    }

    public function getValidator(string $alias): ValueValidatorInterface
    {
        return $this->pool->get($alias);
    }
}
