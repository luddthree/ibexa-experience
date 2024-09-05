<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository;

use Ibexa\Contracts\Core\Collection\MapInterface;
use Ibexa\Contracts\Core\Collection\MutableArrayMap;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\ProductCatalog\PriceResolverInterface;
use Ibexa\Contracts\ProductCatalog\ProductPriceContextResolverInterface;
use Ibexa\Contracts\ProductCatalog\ProductPriceServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomPriceAwareInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\PriceContextInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\PriceQuery;
use Ibexa\Contracts\ProductCatalog\Values\Price\Query\Criterion\Currency;
use Ibexa\Contracts\ProductCatalog\Values\Price\Query\Criterion\CustomerGroup;
use Ibexa\Contracts\ProductCatalog\Values\Price\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\ProductCatalog\Values\Price\Query\Criterion\Product;
use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;

final class PriceResolver implements PriceResolverInterface
{
    private ProductPriceServiceInterface $priceService;

    private ProductPriceContextResolverInterface $contextResolver;

    public function __construct(
        ProductPriceServiceInterface $priceService,
        ProductPriceContextResolverInterface $contextResolver
    ) {
        $this->priceService = $priceService;
        $this->contextResolver = $contextResolver;
    }

    public function resolvePrice(
        ProductInterface $product,
        ?PriceContextInterface $context = null
    ): ?PriceInterface {
        $context = $this->contextResolver->resolveContext($context);

        $currency = $context->getCurrency();
        if ($currency === null) {
            return null;
        }

        try {
            $price = $this->priceService->getPriceByProductAndCurrency($product, $currency);
        } catch (NotFoundException $e) {
            return null;
        }

        $customerGroup = $context->getCustomerGroup();
        if ($customerGroup !== null) {
            $customerGroupPrice = $this->priceService->findOneForCustomerGroup($price, $customerGroup);
            if ($customerGroupPrice !== null) {
                return $customerGroupPrice;
            }
        }

        return $price;
    }

    public function resolvePrices(
        array $products,
        ?PriceContextInterface $context = null
    ): MapInterface {
        $context = $this->contextResolver->resolveContext($context);

        $map = new MutableArrayMap();
        foreach ($products as $product) {
            $map->set($product->getCode(), null);
        }

        $currency = $context->getCurrency();
        if ($currency === null) {
            return $map;
        }

        $criteria = new LogicalAnd(
            new Product(array_map(static fn (ProductInterface $product): string => $product->getCode(), $products)),
            new Currency($currency)
        );

        $customerGroup = $context->getCustomerGroup();
        if ($customerGroup !== null) {
            $criteria = new LogicalAnd($criteria, new CustomerGroup($customerGroup));
        }

        $query = new PriceQuery();
        $query->setQuery($criteria);
        // Each product can have base price and optional custom price
        $query->setLimit(2 * count($products));

        $prices = $this->priceService->findPrices($query);
        foreach ($prices as $price) {
            $code = $price->getProduct()->getCode();
            if (!$map->has($code) || $price instanceof CustomPriceAwareInterface) {
                $map->set($code, $price);
            }
        }

        return $map;
    }
}
