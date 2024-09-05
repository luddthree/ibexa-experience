<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\ProductType;

use Exception;
use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Bundle\ProductCatalog\Form\Data\ProductTypeBulkDeleteData;
use Ibexa\Bundle\ProductCatalog\Form\Type\ProductTypeBulkDeleteType;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\ProductCatalog\Local\LocalProductTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\ProductType\Delete;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class BulkDeleteController extends Controller
{
    private Repository $repository;

    private LocalProductTypeServiceInterface $productTypeService;

    private FormFactoryInterface $formFactory;

    private SubmitHandler $submitHandler;

    private TranslatableNotificationHandlerInterface $notificationHandler;

    public function __construct(
        Repository $repository,
        LocalProductTypeServiceInterface $productTypeService,
        FormFactoryInterface $formFactory,
        SubmitHandler $submitHandler,
        TranslatableNotificationHandlerInterface $notificationHandler
    ) {
        $this->repository = $repository;
        $this->productTypeService = $productTypeService;
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
        $this->notificationHandler = $notificationHandler;
    }

    /**
     * @throws \Symfony\Component\HttpFoundation\Exception\BadRequestException
     */
    public function executeAction(Request $request): Response
    {
        $this->denyAccessUnlessGranted(new Delete());

        $form = $this->createBulkDeleteForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle(
                $form,
                function (ProductTypeBulkDeleteData $data): ?Response {
                    $this->handleFormSubmit($data);

                    return null;
                }
            );

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirectToRoute('ibexa.product_catalog.product_type.list');
    }

    private function createBulkDeleteForm(): FormInterface
    {
        return $this->formFactory->create(ProductTypeBulkDeleteType::class, null, [
            'method' => Request::METHOD_POST,
            'action' => $this->generateUrl('ibexa.product_catalog.product_type.bulk_delete'),
        ]);
    }

    /**
     * @throws \Exception
     */
    private function handleFormSubmit(ProductTypeBulkDeleteData $data): void
    {
        $productTypeNames = [];
        $this->repository->beginTransaction();
        try {
            foreach ($data->getProductTypes() as $productType) {
                $this->productTypeService->deleteProductType($productType);
                $productTypeNames[] = $productType->getName();
            }

            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }

        $this->notificationHandler->success(
            /** @Desc("{1}Product Type '%deletedNames%' deleted.|]1,Inf[ Product Types '%deletedNames%' deleted.") */
            'product_type.delete.success',
            [
                '%deletedNames%' => implode("', '", $productTypeNames),
                '%count%' => count($productTypeNames),
            ],
            'ibexa_product_catalog'
        );
    }
}
