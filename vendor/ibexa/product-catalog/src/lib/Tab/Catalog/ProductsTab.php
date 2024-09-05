<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Tab\Catalog;

use Ibexa\Bundle\ProductCatalog\Form\Data\ProductListFormData;
use Ibexa\Bundle\ProductCatalog\Form\FormMapper\CatalogMapper;
use Ibexa\Bundle\ProductCatalog\Form\Type\Catalog\CatalogType;
use Ibexa\Bundle\ProductCatalog\Form\Type\ProductSearchType;
use Ibexa\Contracts\AdminUi\Tab\AbstractEventDispatchingTab;
use Ibexa\Contracts\AdminUi\Tab\OrderedTabInterface;
use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterDefinitionProviderInterface;
use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\QueryTypeRegistryInterface;
use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause;
use Ibexa\ProductCatalog\Pagerfanta\Adapter\ProductListAdapter;
use Ibexa\ProductCatalog\QueryType\Product\SearchQueryType;
use JMS\TranslationBundle\Annotation\Desc;
use Pagerfanta\Pagerfanta;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class ProductsTab extends AbstractEventDispatchingTab implements OrderedTabInterface
{
    public const URI_FRAGMENT = 'ibexa-tab-catalog-products';

    private ProductServiceInterface $productService;

    private ConfigResolverInterface $configResolver;

    private RequestStack $requestStack;

    private FormFactoryInterface $formFactory;

    private QueryTypeRegistryInterface $queryTypeRegistry;

    private FilterDefinitionProviderInterface $filterDefinitionProvider;

    private LanguageService $languageService;

    private CatalogMapper $catalogMapper;

    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        EventDispatcherInterface $eventDispatcher,
        ProductServiceInterface $productService,
        ConfigResolverInterface $configResolver,
        RequestStack $requestStack,
        FormFactoryInterface $formFactory,
        QueryTypeRegistryInterface $queryTypeRegistry,
        FilterDefinitionProviderInterface $filterDefinitionProvider,
        LanguageService $languageService,
        CatalogMapper $catalogMapper
    ) {
        parent::__construct($twig, $translator, $eventDispatcher);

        $this->productService = $productService;
        $this->configResolver = $configResolver;
        $this->requestStack = $requestStack;
        $this->formFactory = $formFactory;
        $this->queryTypeRegistry = $queryTypeRegistry;
        $this->filterDefinitionProvider = $filterDefinitionProvider;
        $this->languageService = $languageService;
        $this->catalogMapper = $catalogMapper;
    }

    public function getTemplate(): string
    {
        return '@ibexadesign/product_catalog/catalog/tab/products.html.twig';
    }

    public function getTemplateParameters(array $contextParameters = []): array
    {
        /** @var \Ibexa\Contracts\ProductCatalog\Values\CatalogInterface $catalog */
        $catalog = $contextParameters['catalog'];
        $parameters['criteria'][] = $catalog->getQuery();

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

        return [
            'catalog' => $catalog,
            'products' => $this->getProducts($query, $this->resolveCurrentPage()),
            'catalog_form' => $this->createCatalogForm($catalog)->createView(),
            'search_form' => $searchForm->createView(),
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
            'tab.name.products',
            [],
            'ibexa_product_catalog'
        );
    }

    public function getOrder(): int
    {
        return 100;
    }

    /**
     * @return iterable<\Ibexa\ProductCatalog\Local\Repository\Values\Product>
     */
    private function getProducts(ProductQuery $query, int $currentPage): iterable
    {
        $products = new Pagerfanta(new ProductListAdapter($this->productService, $query));
        $products->setMaxPerPage($this->configResolver->getParameter('product_catalog.pagination.products_limit'));
        $products->setCurrentPage($currentPage);

        return $products;
    }

    private function createCatalogForm(CatalogInterface $catalog): FormInterface
    {
        $mainLanguageCode = $catalog->getFirstLanguage();
        $catalogData = $this->catalogMapper->mapToFormData($catalog, [
            'language' => $this->languageService->loadLanguage($mainLanguageCode),
        ]);

        return $this->formFactory->create(CatalogType::class, $catalogData, [
            'method' => Request::METHOD_POST,
            'filter_definitions' => $this->filterDefinitionProvider->getFilterDefinitions(),
        ]);
    }

    private function createSearchForm(ProductListFormData $data): FormInterface
    {
        return $this->formFactory->create(ProductSearchType::class, $data, [
            'method' => Request::METHOD_GET,
            'csrf_protection' => false,
        ]);
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
