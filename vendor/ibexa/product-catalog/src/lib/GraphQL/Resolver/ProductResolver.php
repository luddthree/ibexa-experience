<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\GraphQL\Resolver;

use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductType;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\ProductCatalog\GraphQL\Query\FilterRegistry;
use Ibexa\ProductCatalog\GraphQL\Query\ProductQueryMapper;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Relay\Connection\Paginator;

/**
 * @internal
 */
final class ProductResolver
{
    private ProductServiceInterface $productService;

    private ProductQueryMapper $productQueryMapper;

    private FilterRegistry $filterRegistry;

    public function __construct(
        ProductServiceInterface $productService,
        ProductQueryMapper $productQueryMapper,
        FilterRegistry $filterRegistry
    ) {
        $this->productService = $productService;
        $this->productQueryMapper = $productQueryMapper;
        $this->filterRegistry = $filterRegistry;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function resolveProduct(Argument $argument): ProductInterface
    {
        return $this->productService->getProduct($argument['code']);
    }

    /**
     * @return \GraphQL\Executor\Promise\Promise|\Overblog\GraphQLBundle\Relay\Connection\Output\Connection
     */
    public function resolveProductsByType(string $productTypeIdentifier, Argument $argument)
    {
        $input = $this->supplyBuiltInFilters($argument);
        $input['criteria'][] = new ProductType([$productTypeIdentifier]);
        $input['sortBy'] = $argument['sortBy'];

        $query = $this->productQueryMapper->mapInputToQuery($input);

        return $this->getSuppliedPaginator($argument, $query);
    }

    /**
     * @return \GraphQL\Executor\Promise\Promise|\Overblog\GraphQLBundle\Relay\Connection\Output\Connection
     */
    public function resolveProducts(Argument $argument)
    {
        $input = $this->supplyBuiltInFilters($argument);
        $input['sortBy'] = $argument['sortBy'];

        $query = $this->productQueryMapper->mapInputToQuery($input);

        return $this->getSuppliedPaginator($argument, $query);
    }

    /**
     * @return array<mixed>
     */
    private function supplyBuiltInFilters(Argument $argument): array
    {
        $input = [];
        foreach ($this->filterRegistry->getFilters() as $filter) {
            $name = $filter->getName();

            if (isset($argument[$name])) {
                $input['criteria'][] = $filter->getCriterion([$argument[$name]]);
            }
        }

        return $input;
    }

    /**
     * @return \GraphQL\Executor\Promise\Promise|\Overblog\GraphQLBundle\Relay\Connection\Output\Connection
     */
    private function getSuppliedPaginator(Argument $argument, ProductQuery $query)
    {
        $paginator = new Paginator(function (int $offset, ?int $limit) use ($query): array {
            $query->setOffset($offset);
            $query->setLimit($limit ?? ProductQuery::DEFAULT_LIMIT);

            return $this->productService->findProducts($query)->getProducts();
        });

        return $paginator->auto(
            $argument,
            function () use ($query): int {
                return $this->productService->findProducts($query)->getTotalCount();
            }
        );
    }
}
