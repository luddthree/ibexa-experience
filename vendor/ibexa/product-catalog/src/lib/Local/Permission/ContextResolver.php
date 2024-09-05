<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Permission;

use Ibexa\Contracts\ProductCatalog\Permission\ContextInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\PolicyInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;

final class ContextResolver
{
    /** @var iterable<\Ibexa\Contracts\ProductCatalog\Permission\ContextProvider\ContextProviderInterface> */
    private iterable $contextProviders;

    /**
     * @param iterable<\Ibexa\Contracts\ProductCatalog\Permission\ContextProvider\ContextProviderInterface> $contextProviders
     */
    public function __construct(
        iterable $contextProviders
    ) {
        $this->contextProviders = $contextProviders;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function resolve(PolicyInterface $policy): ContextInterface
    {
        foreach ($this->contextProviders as $contextProvider) {
            if ($contextProvider->accept($policy)) {
                return $contextProvider->getPermissionContext($policy);
            }
        }

        throw new InvalidArgumentException(
            'policy',
            'Unable to find ContextProvider that can accept ' . get_class($policy)
        );
    }
}
