<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\Catalog;

use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Bundle\ProductCatalog\Form\Data\Catalog\CatalogDeleteData;
use Ibexa\Bundle\ProductCatalog\Form\Type\Catalog\CatalogDeleteType;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\ProductCatalog\CatalogServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DeleteController extends Controller
{
    private CatalogServiceInterface $catalogService;

    private TranslatableNotificationHandlerInterface $notificationHandler;

    private SubmitHandler $submitHandler;

    public function __construct(
        CatalogServiceInterface $catalogService,
        TranslatableNotificationHandlerInterface $notificationHandler,
        SubmitHandler $submitHandler
    ) {
        $this->catalogService = $catalogService;
        $this->notificationHandler = $notificationHandler;
        $this->submitHandler = $submitHandler;
    }

    public function executeAction(
        Request $request,
        CatalogInterface $catalog
    ): Response {
        $form = $this->createDeleteForm($catalog);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle(
                $form,
                function (CatalogDeleteData $data): RedirectResponse {
                    return $this->handleFormSubmission($data);
                }
            );

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirectToRoute('ibexa.product_catalog.catalog.list');
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    private function handleFormSubmission(CatalogDeleteData $data): RedirectResponse
    {
        $catalog = $data->getCatalog();

        $this->catalogService->deleteCatalog($catalog);

        $this->notificationHandler->success(
            /** @Desc("{1}Catalog '%deletedNames%' deleted.|]1,Inf[ Catalogs '%deletedNames%' deleted.") */
            'catalog.delete.success',
            [
                '%deletedNames%' => $catalog->getName(),
                '%count%' => 1,
            ],
            'ibexa_product_catalog'
        );

        return $this->redirectToRoute('ibexa.product_catalog.catalog.list');
    }

    private function createDeleteForm(CatalogInterface $catalog): FormInterface
    {
        $actionUrl = $this->generateUrl(
            'ibexa.product_catalog.catalog.delete',
            [
                'catalogId' => $catalog->getId(),
            ]
        );

        return $this->createForm(
            CatalogDeleteType::class,
            new CatalogDeleteData($catalog),
            [
                'method' => Request::METHOD_POST,
                'action' => $actionUrl,
            ]
        );
    }
}
