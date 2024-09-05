<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\Catalog;

use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Bundle\ProductCatalog\Form\Data\Catalog\CatalogUpdateData;
use Ibexa\Bundle\ProductCatalog\Form\DataMapper\Catalog\CatalogUpdateMapper;
use Ibexa\Bundle\ProductCatalog\Form\FormMapper\CatalogMapper;
use Ibexa\Bundle\ProductCatalog\Form\Type\Catalog\CatalogUpdateType;
use Ibexa\Bundle\ProductCatalog\View\CatalogUpdateView;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterDefinitionProviderInterface;
use Ibexa\Contracts\ProductCatalog\CatalogServiceInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Catalog\Edit;
use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause\ProductName;
use Ibexa\ProductCatalog\Local\Repository\Values\Catalog\Catalog;
use Ibexa\ProductCatalog\Pagerfanta\Adapter\ProductListAdapter;
use Ibexa\ProductCatalog\Tab\Catalog\TranslationsTab;
use JMS\TranslationBundle\Annotation\Desc;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class UpdateController extends Controller
{
    private TranslatableNotificationHandlerInterface $notificationHandler;

    private LanguageService $languageService;

    private CatalogServiceInterface $catalogService;

    private CatalogUpdateMapper $catalogUpdateMapper;

    private CatalogMapper $catalogMapper;

    private SubmitHandler $submitHandler;

    private FilterDefinitionProviderInterface $filterDefinitionProvider;

    private ProductServiceInterface $productService;

    private ConfigResolverInterface $configResolver;

    public function __construct(
        TranslatableNotificationHandlerInterface $notificationHandler,
        LanguageService $languageService,
        CatalogServiceInterface $catalogService,
        CatalogUpdateMapper $catalogUpdateMapper,
        CatalogMapper $catalogMapper,
        SubmitHandler $submitHandler,
        FilterDefinitionProviderInterface $filterDefinitionProvider,
        ProductServiceInterface $productService,
        ConfigResolverInterface $configResolver
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->languageService = $languageService;
        $this->catalogService = $catalogService;
        $this->catalogUpdateMapper = $catalogUpdateMapper;
        $this->catalogMapper = $catalogMapper;
        $this->submitHandler = $submitHandler;
        $this->filterDefinitionProvider = $filterDefinitionProvider;
        $this->productService = $productService;
        $this->configResolver = $configResolver;
    }

    /**
     * @return \Ibexa\Bundle\ProductCatalog\View\CatalogUpdateView|\Symfony\Component\HttpFoundation\Response
     */
    public function renderAction(
        Request $request,
        Catalog $catalog,
        ?Language $language = null,
        ?Language $baseLanguage = null
    ) {
        $this->denyAccessUnlessGranted(new Edit());

        $mainLanguageCode = $catalog->getFirstLanguage();
        $language = $language ?? $this->languageService->loadLanguage($mainLanguageCode);

        $catalogData = $this->catalogMapper->mapToFormData($catalog, [
            'baseLanguage' => $baseLanguage,
            'language' => $language,
        ]);
        $translationMode = $mainLanguageCode !== $language->languageCode;
        $form = $this->createForm(CatalogUpdateType::class, $catalogData, [
            'action' => $this->generateUrl('ibexa.product_catalog.catalog.update', [
                'catalogId' => $catalog->getId(),
                'fromLanguageCode' => $baseLanguage->languageCode ?? null,
                'toLanguageCode' => $language->languageCode,
            ]),
            'method' => Request::METHOD_POST,
            'translation_mode' => $mainLanguageCode !== $language->languageCode,
            'catalog' => $catalog,
            'filter_definitions' => $this->filterDefinitionProvider->getFilterDefinitions(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle(
                $form,
                function (CatalogUpdateData $data) use ($catalog, $translationMode): Response {
                    $catalogUpdateStruct = $this->catalogUpdateMapper->mapToStruct($data);
                    $catalog = $this->catalogService->updateCatalog($catalog, $catalogUpdateStruct);

                    $this->notificationHandler->success(
                        /** @Desc("Catalog '%name%' updated.") */
                        'catalog.update.success',
                        ['%name%' => $catalog->getName()],
                        'ibexa_product_catalog'
                    );

                    return $this->redirectToRoute('ibexa.product_catalog.catalog.view', [
                        'catalogId' => $catalog->getId(),
                        '_fragment' => $translationMode ? TranslationsTab::URI_FRAGMENT : null,
                    ]);
                }
            );

            if ($result instanceof Response) {
                return $result;
            }
        }
        $query = new ProductQuery(
            null,
            $catalog->getQuery(),
            [new ProductName(ProductQuery::SORT_ASC)]
        );
        $products = new Pagerfanta(new ProductListAdapter($this->productService, $query));
        $products->setMaxPerPage($this->configResolver->getParameter('product_catalog.pagination.products_limit'));
        $products->setCurrentPage($request->query->getInt('page', 1));

        return new CatalogUpdateView(
            '@ibexadesign/product_catalog/catalog/edit.html.twig',
            $catalog,
            $form,
            $products,
        );
    }
}
