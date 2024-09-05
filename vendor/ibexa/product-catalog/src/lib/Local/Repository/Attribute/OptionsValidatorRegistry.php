<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Attribute;

use Ibexa\Contracts\ProductCatalog\Local\Attribute\OptionsValidatorInterface;
use Ibexa\ProductCatalog\Common\Pool;

final class OptionsValidatorRegistry implements OptionsValidatorRegistryInterface
{
    /** @var \Ibexa\ProductCatalog\Common\Pool<\Ibexa\Contracts\ProductCatalog\Local\Attribute\OptionsValidatorInterface> */
    private Pool $pool;

    /**
     * @param iterable<\Ibexa\Contracts\ProductCatalog\Local\Attribute\OptionsValidatorInterface> $validators
     */
    public function __construct(iterable $validators = [])
    {
        $this->pool = new Pool(OptionsValidatorInterface::class, $validators);
        $this->pool->setExceptionArgumentName('type');
        $this->pool->setExceptionMessageTemplate('Could not find %s for \'%s\' attribute type');
    }

    public function hasValidator(string $typeIdentifier): bool
    {
        return $this->pool->has($typeIdentifier);
    }

    public function getValidator(string $typeIdentifier): OptionsValidatorInterface
    {
        return $this->pool->get($typeIdentifier);
    }
}
