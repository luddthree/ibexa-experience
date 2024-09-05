<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Tab\Category;

use Ibexa\Bundle\ProductCatalog\Form\Data\ProductListFormData;
use Ibexa\Bundle\ProductCatalog\Form\Type\ProductSearchType;
use Ibexa\Contracts\AdminUi\Tab\AbstractEventDispatchingTab;
use Ibexa\Contracts\AdminUi\Tab\ConditionalTabInterface;
use Ibexa\Contracts\AdminUi\Tab\OrderedTabInterface;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\QueryTypeRegistryInterface;
use Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductCategory;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\ProductCatalog\Pagerfanta\Adapter\ProductListAdapter;
use Ibexa\ProductCatalog\QueryType\Product\SearchQueryType;
use Ibexa\Taxonomy\Exception\TaxonomyNotFoundException;
use Ibexa\Taxonomy\Form\Data\TaxonomyContentAssignData;
use Ibexa\Taxonomy\Form\Data\TaxonomyContentUnassignData;
use Ibexa\Taxonomy\Form\Type\TaxonomyContentAssignType;
use Ibexa\Taxonomy\Form\Type\TaxonomyContentUnassignType;
use Ibexa\Taxonomy\Service\TaxonomyConfiguration;
use JMS\TranslationBundle\Annotation\Desc;
use Pagerfanta\Pagerfanta;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class ProductsTab extends AbstractEventDispatchingTab implements OrderedTabInterface, ConditionalTabInterface
{
    public const URI_FRAGMENT = 'ibexa-tab-location-view-products';

    private const PRODUCTS_PAGINATION_LIMIT = 'product_catalog.pagination.products_limit';
    private const CATEGORIES_TAXONOMY = 'product_categories';

    private ProductServiceInterface $productService;

    private ConfigResolverInterface $configResolver;

    private RequestStack $requestStack;

    private TaxonomyConfiguration $taxonomyConfiguration;

    private TaxonomyServiceInterface $taxonomyService;

    private FormFactoryInterface $formFactory;

    /**
     * @var \Ibexa\Contracts\ProductCatalog\QueryTypeRegistryInterface
     */
    private QueryTypeRegistryInterface $queryTypeRegistry;

    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        EventDispatcherInterface $eventDispatcher,
        ProductServiceInterface $productService,
        ConfigResolverInterface $configResolver,
        RequestStack $requestStack,
        TaxonomyConfiguration $taxonomyConfiguration,
        TaxonomyServiceInterface $taxonomyService,
        FormFactoryInterface $formFactory,
        QueryTypeRegistryInterface $queryTypeRegistry
    ) {
        parent::__construct($twig, $translator, $eventDispatcher);

        $this->productService = $productService;
        $this->configResolver = $configResolver;
        $this->requestStack = $requestStack;
        $this->taxonomyConfiguration = $taxonomyConfiguration;
        $this->taxonomyService = $taxonomyService;
        $this->formFactory = $formFactory;
        $this->queryTypeRegistry = $queryTypeRegistry;
    }

    /**
     * @param array<string,mixed> $parameters
     *
     * @throws \Ibexa\Taxonomy\Exception\TaxonomyEntryNotFoundException
     * @throws \Ibexa\Taxonomy\Exception\TaxonomyConfigurationNotFoundException
     */
    public function evaluate(array $parameters): bool
    {
        try {
            $contentType = $parameters['contentType'];
            $taxonomy = $this->taxonomyConfiguration->getTaxonomyForContentType($contentType);
            if ($taxonomy !== self::CATEGORIES_TAXONOMY) {
                return false;
            }

            /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $content */
            $content = $parameters['content'];
            $taxonomyEntry = $this->taxonomyService->loadEntryByContentId($content->id);
            if ($taxonomyEntry->parent === null) {
                return false;
            }

            $contentTypeConfigIdentifier = $this->taxonomyConfiguration->getConfigForTaxonomy(
                $taxonomy,
                'content_type'
            );

            return $contentType->identifier === $contentTypeConfigIdentifier;
        } catch (TaxonomyNotFoundException $e) {
            return false;
        }
    }

    public function getTemplate(): string
    {
        return '@ibexadesign/product_catalog/category/tab/products.html.twig';
    }

    public function getTemplateParameters(array $contextParameters = []): array
    {
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $content */
        $content = $contextParameters['content'];
        $taxonomyEntry = $this->taxonomyService->loadEntryByContentId($content->id);
        $parameters = [];
        $parameters['criteria'] = [
            new ProductCategory([$taxonomyEntry->id]),
        ];

        $parameters['sort_clauses'] = [
            new SortClause\ProductName(ProductQuery::SORT_ASC),
        ];

        $data = new ProductListFormData();
        $searchForm = $this->createSearchForm($data);
        $searchForm->handleRequest($this->requestStack->getCurrentRequest());

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $parameters['query_string'] = $data->getQuery();
            $parameters['sort_clauses'] = [
                $data->getSortClause() ?? new SortClause\ProductName(ProductQuery::SORT_ASC),
            ];
        }
        $query = ($this->queryTypeRegistry->getQueryType(SearchQueryType::NAME))->getQuery($parameters);

        $products = new Pagerfanta(new ProductListAdapter($this->productService, $query));
        $products->setMaxPerPage(
            $this->configResolver->getParameter(self::PRODUCTS_PAGINATION_LIMIT)
        );
        $products->setCurrentPage($this->resolveCurrentPage());

        /** @var \Ibexa\ProductCatalog\Local\Repository\Values\ProductList $productList */
        $productList = $products->getCurrentPageResults();

        $taxonomyProductsAssignForm = $this->createTaxonomyProductsAssignForm($taxonomyEntry);
        $taxonomyProductsUnassignForm = $this->createTaxonomyProductsUnassignForm(
            $taxonomyEntry,
            $productList->getProducts()
        );

        return [
            'products' => $products,
            'no_products' => $productList->getTotalCount() == 0,
            'taxonomy_entry' => $taxonomyEntry,
            'search_form' => $searchForm->createView(),
            'form_taxonomy_products_assign' => $taxonomyProductsAssignForm->createView(),
            'form_taxonomy_products_unassign' => $taxonomyProductsUnassignForm->createView(),
        ];
    }

    public function getIdentifier(): string
    {
        return 'products';
    }

    public function getName(): string
    {
        return $this->translator->trans(
            /** @Desc("Products") */
            'tab.name.category.products',
            [],
            'ibexa_product_catalog'
        );
    }

    public function getOrder(): int
    {
        return 50;
    }

    private function createSearchForm(ProductListFormData $data): FormInterface
    {
        return $this->formFactory->create(ProductSearchType::class, $data, [
            'method' => Request::METHOD_GET,
            'csrf_protection' => false,
        ]);
    }

    private function createTaxonomyProductsAssignForm(TaxonomyEntry $taxonomyEntry): FormInterface
    {
        return $this->formFactory->create(
            TaxonomyContentAssignType::class,
            new TaxonomyContentAssignData($taxonomyEntry),
            ['taxonomy' => $taxonomyEntry->name],
        );
    }

    /**
     * @param array<\Ibexa\Contracts\ProductCatalog\Values\ProductInterface> $products
     */
    private function createTaxonomyProductsUnassignForm(TaxonomyEntry $taxonomyEntry, array $products): FormInterface
    {
        $contents = [];
        foreach ($products as $product) {
            if (!$product instanceof ContentAwareProductInterface) {
                continue;
            }

            $contents[$product->getContent()->id] = false;
        }

        return $this->formFactory->create(
            TaxonomyContentUnassignType::class,
            new TaxonomyContentUnassignData($taxonomyEntry, $contents),
            ['taxonomy' => $taxonomyEntry->name],
        );
    }

    private function resolveCurrentPage(): int
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request !== null) {
            return $request->query->getInt('page', 1);
        }

        return 1;
    }
}
