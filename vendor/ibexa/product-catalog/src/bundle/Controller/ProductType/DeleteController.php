<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\ProductType;

use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Bundle\ProductCatalog\Form\Data\ProductTypeDeleteData;
use Ibexa\Bundle\ProductCatalog\Form\Type\ProductTypeDeleteType;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\ProductCatalog\Local\LocalProductTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\ProductType\Delete;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DeleteController extends Controller
{
    private LocalProductTypeServiceInterface $productTypeService;

    private TranslatableNotificationHandlerInterface $notificationHandler;

    private SubmitHandler $submitHandler;

    public function __construct(
        LocalProductTypeServiceInterface $productTypeService,
        TranslatableNotificationHandlerInterface $notificationHandler,
        SubmitHandler $submitHandler
    ) {
        $this->productTypeService = $productTypeService;
        $this->notificationHandler = $notificationHandler;
        $this->submitHandler = $submitHandler;
    }

    public function executeAction(Request $request, ProductTypeInterface $productType): Response
    {
        $this->denyAccessUnlessGranted(new Delete());

        $form = $this->createProductTypeDeleteForm($productType);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle(
                $form,
                function (ProductTypeDeleteData $data): Response {
                    return $this->handleFormSubmission($data);
                }
            );

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirectToRoute('ibexa.product_catalog.product_type.list');
    }

    private function createProductTypeDeleteForm(ProductTypeInterface $productType): FormInterface
    {
        return $this->createForm(
            ProductTypeDeleteType::class,
            new ProductTypeDeleteData($productType),
            [
                'method' => Request::METHOD_POST,
            ]
        );
    }

    private function handleFormSubmission(ProductTypeDeleteData $data): Response
    {
        $productType = $data->getProductType();

        $this->productTypeService->deleteProductType($productType);

        $this->notificationHandler->success(
            /** @Desc("{1}Product Type '%deletedNames%' deleted.|]1,Inf[ Product Types '%deletedNames%' deleted.") */
            'product_type.delete.success',
            [
                '%deletedNames%' => $productType->getName(),
                '%count%' => 1,
            ],
            'ibexa_product_catalog'
        );

        return $this->redirectToRoute('ibexa.product_catalog.product_type.list');
    }
}
