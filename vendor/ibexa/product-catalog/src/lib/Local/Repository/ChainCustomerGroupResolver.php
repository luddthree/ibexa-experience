<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository;

use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\ProductCatalog\CustomerGroupResolverInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;

final class ChainCustomerGroupResolver implements CustomerGroupResolverInterface
{
    /** @var iterable<\Ibexa\Contracts\ProductCatalog\CustomerGroupResolverInterface> */
    private iterable $resolvers;

    /**
     * @param iterable<\Ibexa\Contracts\ProductCatalog\CustomerGroupResolverInterface> $resolvers
     */
    public function __construct(iterable $resolvers = [])
    {
        $this->resolvers = $resolvers;
    }

    public function resolveCustomerGroup(?User $user = null): ?CustomerGroupInterface
    {
        foreach ($this->resolvers as $resolver) {
            $customerGroup = $resolver->resolveCustomerGroup($user);

            if ($customerGroup !== null) {
                return $customerGroup;
            }
        }

        return null;
    }
}
