<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Values\ProxyFactory;

use Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface;
use Ibexa\Core\Repository\ProxyFactory\ProxyGenerator;
use Ibexa\ProductCatalog\Local\Repository\Values\CustomerGroup;
use ProxyManager\Proxy\LazyLoadingInterface;

final class CustomerGroupProxyFactory implements CustomerGroupProxyFactoryInterface
{
    private ProxyGenerator $proxyGenerator;

    private CustomerGroupServiceInterface $customerGroupService;

    public function __construct(
        ProxyGenerator $proxyGenerator,
        CustomerGroupServiceInterface $customerGroupService
    ) {
        $this->customerGroupService = $customerGroupService;
        $this->proxyGenerator = $proxyGenerator;
    }

    public function createCustomerGroupProxy(int $customerGroupId, ?array $prioritizedLanguages): CustomerGroup
    {
        $initializer = function (
            &$wrappedObject,
            LazyLoadingInterface $proxy,
            $method,
            array $parameters,
            &$initializer
        ) use ($customerGroupId, $prioritizedLanguages): bool {
            $initializer = null;
            $wrappedObject = $this->customerGroupService->getCustomerGroup($customerGroupId, $prioritizedLanguages);

            return true;
        };

        return $this->proxyGenerator->createProxy(CustomerGroup::class, $initializer);
    }
}
