<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\Product;

use Ibexa\Bundle\ProductCatalog\Form\Data\ProductListFormData;
use Ibexa\Bundle\ProductCatalog\Form\Type\ProductSearchType;
use Ibexa\Bundle\ProductCatalog\View\ProductListView;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Product\View;
use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\QueryTypeRegistryInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\ProductCatalog\Pagerfanta\Adapter\ProductListAdapter;
use Ibexa\ProductCatalog\QueryType\Product\SearchQueryType;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

final class ListController extends Controller
{
    private ProductServiceInterface $productService;

    private ConfigResolverInterface $configResolver;

    private QueryTypeRegistryInterface $queryTypeRegistry;

    private TaxonomyServiceInterface $taxonomyService;

    private string $productTaxonomyName;

    public function __construct(
        ProductServiceInterface $productService,
        ConfigResolverInterface $configResolver,
        QueryTypeRegistryInterface $queryTypeRegistry,
        TaxonomyServiceInterface $taxonomyService,
        string $productTaxonomyName
    ) {
        $this->productService = $productService;
        $this->configResolver = $configResolver;
        $this->queryTypeRegistry = $queryTypeRegistry;
        $this->taxonomyService = $taxonomyService;
        $this->productTaxonomyName = $productTaxonomyName;
    }

    public function renderAction(Request $request): ProductListView
    {
        $this->denyAccessUnlessGranted(new View());

        $parameters = [];
        $parameters['sort_clauses'] = [new SortClause\ProductName(ProductQuery::SORT_ASC)];

        $data = new ProductListFormData();

        $categoryRoot = $this->taxonomyService->loadRootEntry($this->productTaxonomyName);
        $data->setCategory($categoryRoot);

        $searchForm = $this->createSearchForm($data);
        $searchForm->handleRequest($request);

        $category = $data->getCategory();
        if ($category === null) {
            $parameters['exclude_category_id'] = $categoryRoot->id;
        } else {
            $parameters['category_id'] = $category->id;
        }

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $parameters['query_string'] = $data->getQuery();
            $parameters['sort_clauses'] = [
                $data->getSortClause() ?? new SortClause\ProductName(ProductQuery::SORT_ASC),
            ];
        }
        $query = ($this->queryTypeRegistry->getQueryType(SearchQueryType::NAME))->getQuery($parameters);

        $paramsWithCategoryIdTemplate = $request->query->all();
        if (!array_key_exists('product_search', $paramsWithCategoryIdTemplate)) {
            $paramsWithCategoryIdTemplate['product_search'] = [];
        }
        $paramsWithCategoryIdTemplate['product_search']['category'] = '%category%';
        $categoryWithFormDataUrlTemplate = $this->generateUrl(
            'ibexa.product_catalog.product.list',
            $paramsWithCategoryIdTemplate
        );

        $products = new Pagerfanta(new ProductListAdapter($this->productService, $query));
        $products->setMaxPerPage($this->configResolver->getParameter('product_catalog.pagination.products_limit'));
        $products->setCurrentPage($request->query->getInt('page', 1));

        return new ProductListView(
            '@ibexadesign/product_catalog/product/list.html.twig',
            $products,
            $searchForm,
            $category,
            $categoryWithFormDataUrlTemplate
        );
    }

    private function createSearchForm(ProductListFormData $data): FormInterface
    {
        return $this->createForm(ProductSearchType::class, $data, [
            'method' => Request::METHOD_GET,
            'csrf_protection' => false,
        ]);
    }
}
