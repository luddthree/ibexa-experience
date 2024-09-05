<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\Catalog;

use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Bundle\ProductCatalog\Form\Data\Catalog\CatalogCopyData;
use Ibexa\Bundle\ProductCatalog\Form\DataMapper\Catalog\CatalogCopyMapper;
use Ibexa\Bundle\ProductCatalog\Form\Type\Catalog\CatalogCopyType;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\ProductCatalog\CatalogServiceInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Catalog\Create;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CopyController extends Controller
{
    private TranslatableNotificationHandlerInterface $notificationHandler;

    private CatalogServiceInterface $catalogService;

    private CatalogCopyMapper $catalogCopyMapper;

    private SubmitHandler $submitHandler;

    public function __construct(
        TranslatableNotificationHandlerInterface $notificationHandler,
        CatalogServiceInterface $catalogService,
        CatalogCopyMapper $catalogCopyMapper,
        SubmitHandler $submitHandler
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->catalogService = $catalogService;
        $this->catalogCopyMapper = $catalogCopyMapper;
        $this->submitHandler = $submitHandler;
    }

    public function renderAction(Request $request): Response
    {
        $this->denyAccessUnlessGranted(new Create());

        $form = $this->createCopyForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle(
                $form,
                function (CatalogCopyData $data): RedirectResponse {
                    return $this->handleFormSubmission($data);
                }
            );

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirectToRoute('ibexa.product_catalog.catalog.list');
    }

    private function createCopyForm(): FormInterface
    {
        $actionUrl = $this->generateUrl(
            'ibexa.product_catalog.catalog.copy',
        );

        return $this->createForm(
            CatalogCopyType::class,
            null,
            [
                'method' => Request::METHOD_POST,
                'action' => $actionUrl,
            ]
        );
    }

    private function handleFormSubmission(CatalogCopyData $data): RedirectResponse
    {
        $catalog = $data->getCatalog();

        $catalogCopyStruct = $this->catalogCopyMapper->mapToStruct($data);
        $newCatalog = $this->catalogService->copyCatalog($catalog, $catalogCopyStruct);

        $this->notificationHandler->success(
            /** @Desc("Catalog '%name%' copied.") */
            'catalog.copy.success',
            ['%name%' => $newCatalog->getName()],
            'ibexa_product_catalog'
        );

        return $this->redirectToRoute('ibexa.product_catalog.catalog.update', [
            'catalogId' => $newCatalog->getId(),
        ]);
    }
}
