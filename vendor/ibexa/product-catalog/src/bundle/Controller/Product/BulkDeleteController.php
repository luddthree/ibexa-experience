<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\Product;

use Exception;
use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Bundle\ProductCatalog\Form\Data\ProductBulkDeleteData;
use Ibexa\Bundle\ProductCatalog\Form\Type\ProductBulkDeleteType;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\ProductCatalog\Local\LocalProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Product\Delete;
use Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface;
use Ibexa\ProductCatalog\Tab\Product\VariantsTab;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class BulkDeleteController extends Controller
{
    private LocalProductServiceInterface $productService;

    private TranslatableNotificationHandlerInterface $notificationHandler;

    private SubmitHandler $submitHandler;

    private FormFactoryInterface $formFactory;

    private Repository $repository;

    public function __construct(
        LocalProductServiceInterface $productService,
        TranslatableNotificationHandlerInterface $notificationHandler,
        SubmitHandler $submitHandler,
        FormFactoryInterface $formFactory,
        Repository $repository
    ) {
        $this->productService = $productService;
        $this->notificationHandler = $notificationHandler;
        $this->submitHandler = $submitHandler;
        $this->formFactory = $formFactory;
        $this->repository = $repository;
    }

    public function executeAction(Request $request): Response
    {
        $this->denyAccessUnlessGranted(new Delete());

        $form = $this->createBulkDeleteForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (ProductBulkDeleteData $data): Response {
                $baseProduct = null;
                $productNames = [];
                $this->repository->beginTransaction();
                try {
                    foreach ($data->getProducts() as $product) {
                        if ($baseProduct === null && $product instanceof ProductVariantInterface) {
                            $baseProduct = $product->getBaseProduct();
                        }

                        $this->productService->deleteProduct($product);
                        $productNames[] = $product->getName();
                    }

                    $this->repository->commit();
                } catch (Exception $e) {
                    $this->repository->rollback();
                    throw $e;
                }

                $this->notificationHandler->success(
                    /** @Desc("{1}Product '%deletedNames%' deleted.|]1,Inf[ Products '%deletedNames%' deleted.") */
                    'product.delete.success',
                    [
                        '%deletedNames%' => implode("', '", $productNames),
                        '%count%' => count($productNames),
                    ],
                    'ibexa_product_catalog'
                );

                if ($baseProduct !== null) {
                    return $this->redirectToProductView($baseProduct, VariantsTab::URI_FRAGMENT);
                }

                return $this->redirectToProductList();
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirectToProductList();
    }

    private function createBulkDeleteForm(): FormInterface
    {
        return $this->formFactory->create(ProductBulkDeleteType::class, null, [
            'method' => Request::METHOD_POST,
            'action' => $this->generateUrl('ibexa.product_catalog.product.bulk_delete'),
        ]);
    }
}
