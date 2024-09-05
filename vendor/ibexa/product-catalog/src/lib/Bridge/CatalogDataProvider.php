<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Bridge;

use Ibexa\Bundle\Commerce\Eshop\Catalog\CatalogElementContainer;
use Ibexa\Bundle\Commerce\Eshop\Catalog\CatalogListResult;
use Ibexa\Bundle\Commerce\Eshop\Model\Navigation\UrlMapping;
use Ibexa\Bundle\Commerce\Eshop\Product\OrderableProductNode;
use Ibexa\Bundle\Commerce\Eshop\Services\DataProvider\CatalogDataProvider as BaseCatalogDataProvider;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException;
use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery;
use Ibexa\Core\Base\Exceptions\InvalidArgumentValue;

/**
 * @deprecated since 4.6, will be removed in 5.0. Use ibexa/checkout and ibexa/order-management packages instead
 */
final class CatalogDataProvider extends BaseCatalogDataProvider
{
    private ProductServiceInterface $productService;

    public function __construct(
        CatalogFactory $catalogFactory,
        ProductServiceInterface $productService
    ) {
        $this->catalogFactory = $catalogFactory;
        $this->productService = $productService;
    }

    /**
     * @param int|string $identifier
     * @param int $depth
     * @param mixed $filter
     * @param string|string[]|null $languages
     * @param int $offset
     * @param int $limit
     */
    public function fetchChildrenList($identifier, $depth, $filter, $languages = null, $offset = 0, $limit = 3): CatalogListResult
    {
        $query = new ProductQuery();
        $query->setLimit($limit);
        $query->setOffset($offset);

        $results = $this->productService->findProducts($query);

        return new CatalogListResult(
            array_map(
                [$this->catalogFactory, 'createCatalogNode'],
                $results->getProducts()
            ),
            $results->getTotalCount()
        );
    }

    /**
     * @param int|string $identifier
     * @param int $depth
     * @param mixed $filter
     * @param string|string[]|null $languages
     */
    public function countChildrenList($identifier, $depth, $filter, $languages = null): int
    {
        $query = new ProductQuery();
        $query->setLimit(0);

        return $this->productService->findProducts($query)->getTotalCount();
    }

    /**
     * @param int|string $identifier
     * @param int $depth
     * @param mixed $filter
     * @param string|string[]|null $languages
     */
    public function fetchSubtree($identifier, $depth, $filter, $languages = null)
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @param string|null $identifier
     * @param string|string[]|null $languages
     */
    public function fetchElementByIdentifier($identifier, $languages = null): OrderableProductNode
    {
        if (!is_string($identifier)) {
            throw new InvalidArgumentValue('$identifier', $identifier);
        }

        return $this->catalogFactory->createOrderableProductNode(
            $this->productService->getProduct($identifier),
        );
    }

    public function getIdentifierOfRootElement()
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @param string $url
     * @param string|string[]|null $languages
     */
    public function lookupByUrl($url, $languages = null)
    {
        throw new NotImplementedException(__METHOD__);
    }

    public function fillChildElements(CatalogElementContainer $catalogElement, $withElements): void
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @param string $sku
     * @param array<string,mixed> $params
     * @param string|string[]|null $languages
     *
     * @return \Ibexa\Bundle\Commerce\Eshop\Product\OrderableProductNode|\Ibexa\Bundle\Commerce\Eshop\Product\ProductNode|null
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function fetchElementBySku($sku, array $params = [], $languages = null)
    {
        try {
            $product = $this->productService->getProduct((string)$sku);

            return $this->catalogFactory->createOrderableProductNode($product);
        } catch (NotFoundException $e) {
            return null;
        }
    }

    /**
     * @param string[] $skuList
     * @param array<string,mixed> $params
     * @param string|string[]|null $languages
     *
     * @return \Ibexa\Bundle\Commerce\Eshop\Product\ProductNode[]
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException
     */
    public function fetchElementsBySku(array $skuList, array $params = [], $languages = null): array
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @param array<string,mixed> $params
     *
     * @return mixed
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException
     */
    public function fetchByQuery($query, array $params = [])
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @param array<string,mixed> $params
     *
     * @return mixed
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException
     */
    public function setCatalogUrlMapping(UrlMapping $urlMapping, array $params = [])
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @param string $identifier
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException
     */
    public function getUrlByIdentifier($identifier): string
    {
        throw new NotImplementedException(__METHOD__);
    }
}
