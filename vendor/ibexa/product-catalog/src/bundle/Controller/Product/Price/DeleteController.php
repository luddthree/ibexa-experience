<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\Product\Price;

use Exception;
use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Bundle\ProductCatalog\Controller\Product\Controller;
use Ibexa\Bundle\ProductCatalog\Form\Data\Price\ProductPriceDeleteData;
use Ibexa\Bundle\ProductCatalog\Form\Type\ProductPriceBulkDeleteType;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Product\Delete;
use Ibexa\Contracts\ProductCatalog\ProductPriceServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\Delete\Struct\ProductPriceDeleteStruct;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\ProductCatalog\Tab\Product\PricesTab;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DeleteController extends Controller
{
    private ProductPriceServiceInterface $productPriceService;

    private TranslatableNotificationHandlerInterface $notificationHandler;

    private SubmitHandler $submitHandler;

    private Repository $repository;

    public function __construct(
        ProductPriceServiceInterface $productPriceService,
        TranslatableNotificationHandlerInterface $notificationHandler,
        SubmitHandler $submitHandler,
        Repository $repository
    ) {
        $this->productPriceService = $productPriceService;
        $this->notificationHandler = $notificationHandler;
        $this->submitHandler = $submitHandler;
        $this->repository = $repository;
    }

    public function renderAction(Request $request, ProductInterface $product): Response
    {
        $this->denyAccessUnlessGranted(new Delete($product));

        $form = $this->createBulkDeleteForm($product);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (ProductPriceDeleteData $data): ?Response {
                $productNames = [];
                $priceCodes = [];

                $this->repository->beginTransaction();
                try {
                    foreach ($data->getPrices() as $price) {
                        $struct = new ProductPriceDeleteStruct($price);
                        $this->productPriceService->deletePrice($struct);

                        $productNames[] = $price->getProduct()->getName();
                        $priceCodes[] = $price->getCurrency()->getCode();
                    }

                    $this->repository->commit();
                } catch (Exception $e) {
                    $this->repository->rollback();
                    throw $e;
                }

                $this->notificationHandler->success(
                    /** @Desc("{1}Price for product '%productNames%' in currency '%deletedCodes%' deleted.|]1,Inf[ Prices for products '%productNames%' in currencies '%deletedCodes%' (respectively) deleted.") */
                    'price.delete.success',
                    [
                        '%productNames%' => implode("', '", $productNames),
                        '%deletedCodes%' => implode("', '", $priceCodes),
                        '%count%' => count($priceCodes),
                    ],
                    'ibexa_product_catalog'
                );

                return null;
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirectToProductView($product, PricesTab::URI_FRAGMENT);
    }

    private function createBulkDeleteForm(ProductInterface $product): FormInterface
    {
        return $this->createForm(ProductPriceBulkDeleteType::class, null, [
            'method' => Request::METHOD_POST,
            'action' => $this->generateUrl('ibexa.product_catalog.product.price.delete', [
                'productCode' => $product->getCode(),
            ]),
        ]);
    }
}
