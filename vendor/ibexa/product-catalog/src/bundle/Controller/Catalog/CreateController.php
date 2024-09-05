<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\Catalog;

use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Bundle\ProductCatalog\Form\Data\Catalog\CatalogCreateData;
use Ibexa\Bundle\ProductCatalog\Form\DataMapper\Catalog\CatalogCreateMapper;
use Ibexa\Bundle\ProductCatalog\Form\Type\Catalog\CatalogCreateType;
use Ibexa\Bundle\ProductCatalog\View\CatalogCreateView;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterDefinitionProviderInterface;
use Ibexa\Contracts\ProductCatalog\CatalogServiceInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Catalog\Create;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CreateController extends Controller
{
    private TranslatableNotificationHandlerInterface $notificationHandler;

    private CatalogServiceInterface $catalogService;

    private CatalogCreateMapper $catalogCreateMapper;

    private SubmitHandler $submitHandler;

    private FilterDefinitionProviderInterface $filterDefinitionProvider;

    public function __construct(
        TranslatableNotificationHandlerInterface $notificationHandler,
        CatalogServiceInterface $catalogService,
        CatalogCreateMapper $catalogCreateMapper,
        SubmitHandler $submitHandler,
        FilterDefinitionProviderInterface $filterDefinitionProvider
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->catalogService = $catalogService;
        $this->catalogCreateMapper = $catalogCreateMapper;
        $this->submitHandler = $submitHandler;
        $this->filterDefinitionProvider = $filterDefinitionProvider;
    }

    /**
     * @return \Ibexa\Bundle\ProductCatalog\View\CatalogCreateView|\Symfony\Component\HttpFoundation\Response
     */
    public function renderAction(Request $request)
    {
        $this->denyAccessUnlessGranted(new Create());

        $form = $this->createForm(CatalogCreateType::class, null, [
            'action' => $this->generateUrl('ibexa.product_catalog.catalog.create'),
            'method' => Request::METHOD_POST,
            'filter_definitions' => $this->filterDefinitionProvider->getFilterDefinitions(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (CatalogCreateData $data): Response {
                $catalogCreateStruct = $this->catalogCreateMapper->mapToStruct($data);
                $catalog = $this->catalogService->createCatalog($catalogCreateStruct);

                $this->notificationHandler->success(
                    /** @Desc("Catalog '%name%' created.") */
                    'catalog.create.success',
                    ['%name%' => $catalog->getName()],
                    'ibexa_product_catalog'
                );

                return $this->redirectToRoute('ibexa.product_catalog.catalog.view', [
                    'catalogId' => $catalog->getId(),
                ]);
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return new CatalogCreateView('@ibexadesign/product_catalog/catalog/create.html.twig', $form);
    }
}
