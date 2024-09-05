<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository;

use Ibexa\Contracts\ProductCatalog\CurrencyResolverInterface;
use Ibexa\Contracts\ProductCatalog\CustomerGroupResolverInterface;
use Ibexa\Contracts\ProductCatalog\Exceptions\ConfigurationException;
use Ibexa\Contracts\ProductCatalog\ProductPriceContextResolverInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\PriceContext;
use Ibexa\Contracts\ProductCatalog\Values\Price\PriceContextInterface;

final class ProductPriceContextResolver implements ProductPriceContextResolverInterface
{
    private CurrencyResolverInterface $currencyResolver;

    private CustomerGroupResolverInterface $customerGroupResolver;

    public function __construct(
        CurrencyResolverInterface $currencyResolver,
        CustomerGroupResolverInterface $customerGroupResolver
    ) {
        $this->currencyResolver = $currencyResolver;
        $this->customerGroupResolver = $customerGroupResolver;
    }

    public function resolveContext(?PriceContextInterface $context = null): PriceContextInterface
    {
        $context ??= new PriceContext();

        if ($context->getCurrency() === null) {
            try {
                $context->setCurrency($this->currencyResolver->resolveCurrency());
            } catch (ConfigurationException $e) {
                //do nothing - currency cannot be set
            }
        }

        if ($context->getCustomerGroup() === null) {
            $context->setCustomerGroup($this->customerGroupResolver->resolveCustomerGroup());
        }

        return $context;
    }
}
