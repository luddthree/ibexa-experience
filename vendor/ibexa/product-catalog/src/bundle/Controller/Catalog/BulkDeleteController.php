<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\Catalog;

use Exception;
use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Bundle\ProductCatalog\Form\Data\Catalog\CatalogBulkDeleteData;
use Ibexa\Bundle\ProductCatalog\Form\Type\Catalog\CatalogBulkDeleteType;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\ProductCatalog\CatalogServiceInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Catalog\Delete;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class BulkDeleteController extends Controller
{
    private TranslatableNotificationHandlerInterface $notificationHandler;

    private CatalogServiceInterface $catalogService;

    private SubmitHandler $submitHandler;

    private Repository $repository;

    public function __construct(
        TranslatableNotificationHandlerInterface $notificationHandler,
        CatalogServiceInterface $catalogService,
        SubmitHandler $submitHandler,
        Repository $repository
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->catalogService = $catalogService;
        $this->submitHandler = $submitHandler;
        $this->repository = $repository;
    }

    public function executeAction(Request $request): Response
    {
        $this->denyAccessUnlessGranted(new Delete());

        $form = $this->createBulkDeleteForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (CatalogBulkDeleteData $data): ?Response {
                $catalogNames = [];
                $this->repository->beginTransaction();
                try {
                    foreach ($data->getCatalogs() as $catalog) {
                        $this->catalogService->deleteCatalog($catalog);
                        $catalogNames[] = $catalog->getName();
                    }

                    $this->repository->commit();
                } catch (Exception $e) {
                    $this->repository->rollback();
                    throw $e;
                }

                $this->notificationHandler->success(
                    /** @Desc("{1}Catalog '%deletedNames%' deleted.|]1,Inf[ Catalogs '%deletedNames%' deleted.") */
                    'catalog.delete.success',
                    [
                        '%deletedNames%' => implode("', '", $catalogNames),
                        '%count%' => count($catalogNames),
                    ],
                    'ibexa_product_catalog'
                );

                return null;
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirect($this->generateUrl('ibexa.product_catalog.catalog.list'));
    }

    private function createBulkDeleteForm(): FormInterface
    {
        return $this->createForm(CatalogBulkDeleteType::class, null, [
            'method' => Request::METHOD_POST,
            'action' => $this->generateUrl('ibexa.product_catalog.catalog.bulk_delete'),
        ]);
    }
}
