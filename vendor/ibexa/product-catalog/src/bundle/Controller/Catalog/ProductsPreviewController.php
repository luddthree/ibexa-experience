<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\Catalog;

use Ibexa\Bundle\ProductCatalog\Form\Data\ProductsPreviewFormData;
use Ibexa\Bundle\ProductCatalog\Form\Type\ProductsPreviewType;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterDefinitionProviderInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Catalog\Edit;
use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\QueryTypeRegistryInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause;
use Ibexa\ProductCatalog\Pagerfanta\Adapter\ProductListAdapter;
use Ibexa\ProductCatalog\QueryType\Product\SearchQueryType;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

final class ProductsPreviewController extends Controller
{
    private FilterDefinitionProviderInterface $filterDefinitionProvider;

    private ProductServiceInterface $productService;

    private ConfigResolverInterface $configResolver;

    private SerializerInterface $serializer;

    private QueryTypeRegistryInterface $queryTypeRegistry;

    public function __construct(
        FilterDefinitionProviderInterface $filterDefinitionProvider,
        ProductServiceInterface $productService,
        ConfigResolverInterface $configResolver,
        SerializerInterface $serializer,
        QueryTypeRegistryInterface $queryTypeRegistry
    ) {
        $this->filterDefinitionProvider = $filterDefinitionProvider;
        $this->productService = $productService;
        $this->configResolver = $configResolver;
        $this->serializer = $serializer;
        $this->queryTypeRegistry = $queryTypeRegistry;
    }

    public function listAction(
        Request $request
    ): JsonResponse {
        $this->denyAccessUnlessGranted(new Edit());

        $form = $this->getProductsPreviewForm();
        $form->handleRequest($request);
        $parameters = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $parameters['query_string'] = $form->getData()->getSearch()->getQuery();
            $parameters['filter'] = $this->buildProductFilters($form->getData());
            $parameters['sort_clauses'] = [
                $this->buildProductSortClause($form->getData()),
            ];
        }
        $query = ($this->queryTypeRegistry->getQueryType(SearchQueryType::NAME))->getQuery($parameters);
        $products = new Pagerfanta(new ProductListAdapter($this->productService, $query));
        $products->setMaxPerPage($this->configResolver->getParameter('product_catalog.pagination.products_limit'));
        $products->setCurrentPage($request->request->getInt('page', 1));

        $response = $this->serializer->serialize(
            [
                'products' => $products->getCurrentPageResults(),
                'count' => $products->getNbResults(),
                'current_page' => $products->getCurrentPage(),
                'pages_count' => $products->getNbPages(),
            ],
            'json'
        );

        return new JsonResponse(
            $response,
            Response::HTTP_OK,
            [],
            true
        );
    }

    private function buildProductFilters(ProductsPreviewFormData $data): CriterionInterface
    {
        return $data->getFilters() ?? new Criterion\MatchAll();
    }

    private function buildProductSortClause(ProductsPreviewFormData $data): SortClause
    {
        $sortClause = new SortClause\ProductName(ProductQuery::SORT_ASC);

        if ($data->getSearch() !== null && $data->getSearch()->getSortClause() !== null) {
            $sortClause = $data->getSearch()->getSortClause();
        }

        return $sortClause;
    }

    private function getProductsPreviewForm(): FormInterface
    {
        return $this->createForm(ProductsPreviewType::class, null, [
            'method' => Request::METHOD_POST,
            'csrf_protection' => false,
            'filter_definitions' => $this->filterDefinitionProvider->getFilterDefinitions(),
        ]);
    }
}
