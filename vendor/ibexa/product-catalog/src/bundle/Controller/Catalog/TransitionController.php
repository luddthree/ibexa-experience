<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\Catalog;

use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Bundle\ProductCatalog\Form\Data\Catalog\CatalogTransitionData;
use Ibexa\Bundle\ProductCatalog\Form\Type\Catalog\CatalogTransitionType;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\ProductCatalog\CatalogServiceInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Catalog\Edit;
use Ibexa\Contracts\ProductCatalog\Values\Catalog\CatalogUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\Catalog\Catalog;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class TransitionController extends Controller
{
    private TranslatableNotificationHandlerInterface $notificationHandler;

    private CatalogServiceInterface $catalogService;

    private SubmitHandler $submitHandler;

    public function __construct(
        TranslatableNotificationHandlerInterface $notificationHandler,
        CatalogServiceInterface $catalogService,
        SubmitHandler $submitHandler
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->catalogService = $catalogService;
        $this->submitHandler = $submitHandler;
    }

    public function renderAction(
        Request $request,
        Catalog $catalog
    ): Response {
        $this->denyAccessUnlessGranted(new Edit());

        $form = $this->createTransitionForm($catalog);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle(
                $form,
                function (CatalogTransitionData $data) use ($catalog): ?Response {
                    $this->handleFormSubmission($catalog, $data);

                    return null;
                }
            );

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirectToRoute('ibexa.product_catalog.catalog.view', [
            'catalogId' => $catalog->getId(),
        ]);
    }

    private function handleFormSubmission(CatalogInterface $catalog, CatalogTransitionData $data): void
    {
        $transition = $data->getTransition();

        $catalogUpdateStruct = new CatalogUpdateStruct(
            $catalog->getId(),
            $transition
        );

        $this->catalogService->updateCatalog($catalog, $catalogUpdateStruct);

        $this->notificationHandler->success(
            /** @Desc("Catalog status has been changed.") */
            'catalog.transition.success',
            [],
            'ibexa_product_catalog'
        );
    }

    private function createTransitionForm(Catalog $catalog): FormInterface
    {
        return $this->createForm(
            CatalogTransitionType::class,
            new CatalogTransitionData($catalog->getId()),
            [
                'action' => $this->generateUrl(
                    'ibexa.product_catalog.catalog.update',
                    [
                        'catalogId' => $catalog->getId(),
                        'toLanguageCode' => $catalog->getFirstLanguage(),
                    ]
                ),
                'method' => Request::METHOD_POST,
                'catalog' => $catalog,
            ]
        );
    }
}
