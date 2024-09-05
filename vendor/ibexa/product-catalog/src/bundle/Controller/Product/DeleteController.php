<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\Product;

use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Bundle\ProductCatalog\Form\Data\ProductDeleteData;
use Ibexa\Bundle\ProductCatalog\Form\Type\ProductDeleteType;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\ProductCatalog\Local\LocalProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface;
use Ibexa\ProductCatalog\Tab\Product\VariantsTab;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DeleteController extends Controller
{
    private LocalProductServiceInterface $productService;

    private TranslatableNotificationHandlerInterface $notificationHandler;

    private SubmitHandler $submitHandler;

    public function __construct(
        LocalProductServiceInterface $productService,
        TranslatableNotificationHandlerInterface $notificationHandler,
        SubmitHandler $submitHandler
    ) {
        $this->productService = $productService;
        $this->notificationHandler = $notificationHandler;
        $this->submitHandler = $submitHandler;
    }

    public function execute(Request $request, ProductInterface $product): Response
    {
        $form = $this->createDeleteForm($product);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle(
                $form,
                function (ProductDeleteData $data): RedirectResponse {
                    return $this->handleFormSubmission($data);
                }
            );

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirectToProductView($product);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    private function handleFormSubmission(ProductDeleteData $data): RedirectResponse
    {
        $product = $data->getProduct();

        $this->productService->deleteProduct($product);

        $this->notificationHandler->success(
            /** @Desc("{1}Product '%deletedNames%' deleted.|]1,Inf[ Products '%deletedNames%' deleted.") */
            'product.delete.success',
            [
                '%deletedNames%' => $product->getName(),
                '%count%' => 1,
            ],
            'ibexa_product_catalog'
        );

        return $product instanceof ProductVariantInterface
            ? $this->redirectToProductView($product->getBaseProduct(), VariantsTab::URI_FRAGMENT)
            : $this->redirectToProductList();
    }

    private function createDeleteForm(ProductInterface $product): FormInterface
    {
        $actionUrl = $this->generateUrl(
            'ibexa.product_catalog.product.delete',
            [
                'productCode' => $product->getCode(),
            ]
        );

        return $this->createForm(
            ProductDeleteType::class,
            new ProductDeleteData($product),
            [
                'method' => Request::METHOD_POST,
                'action' => $actionUrl,
            ]
        );
    }
}
