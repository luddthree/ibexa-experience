<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\Product\Availability;

use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Bundle\ProductCatalog\Controller\Product\Controller;
use Ibexa\Bundle\ProductCatalog\Form\Data\Availability\ProductAvailabilityCreateData;
use Ibexa\Bundle\ProductCatalog\Form\DataMapper\ProductAvailabilityCreateMapper;
use Ibexa\Bundle\ProductCatalog\Form\Type\ProductAvailabilityCreateType;
use Ibexa\Bundle\ProductCatalog\View\AvailabilityCreateViewAvailability;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Product\PreEdit;
use Ibexa\Contracts\ProductCatalog\ProductAvailabilityServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\ProductCatalog\Tab\Product\AvailabilityTab;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CreateController extends Controller
{
    private TranslatableNotificationHandlerInterface $notificationHandler;

    private SubmitHandler $submitHandler;

    private ProductAvailabilityServiceInterface $productAvailabilityService;

    private ProductAvailabilityCreateMapper $mapper;

    public function __construct(
        TranslatableNotificationHandlerInterface $notificationHandler,
        SubmitHandler $submitHandler,
        ProductAvailabilityServiceInterface $productAvailabilityService,
        ProductAvailabilityCreateMapper $mapper
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->submitHandler = $submitHandler;
        $this->mapper = $mapper;
        $this->productAvailabilityService = $productAvailabilityService;
    }

    /**
     * @return \Ibexa\Bundle\ProductCatalog\View\AvailabilityCreateViewAvailability|\Symfony\Component\HttpFoundation\Response
     */
    public function renderAction(ProductInterface $product, Request $request)
    {
        $this->denyAccessUnlessGranted(new PreEdit($product));

        $data = new ProductAvailabilityCreateData($product);
        $form = $this->createForm(ProductAvailabilityCreateType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $handler = function (ProductAvailabilityCreateData $createData) use ($product): RedirectResponse {
                $struct = $this->mapper->mapToStruct($createData);
                $this->productAvailabilityService->createProductAvailability($struct);

                $this->notificationHandler->success(
                    /** @Desc("Availability for Product '%product_name%' (%product_code%) created.") */
                    'product.availability.create.success',
                    [
                        '%product_name%' => $product->getName(),
                        '%product_code%' => $product->getCode(),
                    ],
                    'ibexa_product_catalog'
                );

                return $this->redirectToProductView($product, AvailabilityTab::URI_FRAGMENT);
            };
            $result = $this->submitHandler->handle($form, $handler);

            if ($result instanceof Response) {
                return $result;
            }
        }

        return new AvailabilityCreateViewAvailability('@ibexadesign/product_catalog/product/availability/create.html.twig', $product, $form);
    }
}
