<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\Product\Price;

use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Bundle\ProductCatalog\Controller\Product\Controller;
use Ibexa\Bundle\ProductCatalog\Form\Data\Price\Create\CustomerGroupPriceCreateData;
use Ibexa\Bundle\ProductCatalog\Form\Data\Price\CustomerGroupAwareInterface;
use Ibexa\Bundle\ProductCatalog\Form\Data\Price\ProductPricesData;
use Ibexa\Bundle\ProductCatalog\Form\Data\Price\Update\CustomerGroupPriceUpdateData;
use Ibexa\Bundle\ProductCatalog\Form\Data\Price\Update\ProductPriceUpdateData;
use Ibexa\Bundle\ProductCatalog\Form\DataMapper\ProductPrice\ProductPriceMapper;
use Ibexa\Bundle\ProductCatalog\Form\Type\ProductPricesType;
use Ibexa\Bundle\ProductCatalog\View\PriceUpdateView;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Product\PreEdit;
use Ibexa\Contracts\ProductCatalog\ProductPriceServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupQuery;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomPriceAwareInterface;
use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\ProductCatalog\Tab\Product\PricesTab;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class UpdateController extends Controller
{
    private TranslatableNotificationHandlerInterface $notificationHandler;

    private SubmitHandler $submitHandler;

    private ProductPriceServiceInterface $productPriceService;

    private ProductPriceMapper $productPriceMapper;

    private CustomerGroupServiceInterface $customerGroupService;

    public function __construct(
        TranslatableNotificationHandlerInterface $notificationHandler,
        SubmitHandler $submitHandler,
        ProductPriceServiceInterface $productPriceService,
        ProductPriceMapper $productPriceMapper,
        CustomerGroupServiceInterface $customerGroupService
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->submitHandler = $submitHandler;
        $this->productPriceService = $productPriceService;
        $this->productPriceMapper = $productPriceMapper;
        $this->customerGroupService = $customerGroupService;
    }

    /**
     * @return \Ibexa\Bundle\ProductCatalog\View\PriceUpdateView|\Symfony\Component\HttpFoundation\Response
     */
    public function renderAction(ProductInterface $product, CurrencyInterface $currency, Request $request)
    {
        $this->denyAccessUnlessGranted(new PreEdit($product));

        $price = $this->productPriceService->getPriceByProductAndCurrency($product, $currency);

        $form = $this->getUpdateForm($price, $product, $currency);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $handler = function (ProductPricesData $data) use ($product, $currency): RedirectResponse {
                $structs = [];
                foreach ($data as $priceChangeData) {
                    $structs[] = $this->productPriceMapper->mapToStruct($priceChangeData);
                }

                $this->productPriceService->execute($structs);
                $this->notificationHandler->success(
                    /** @Desc("Prices in %price_currency_code% for Product '%product_name%' (%product_code%) updated.") */
                    'product.price.update.success',
                    [
                        '%product_name%' => $product->getName(),
                        '%product_code%' => $product->getCode(),
                        '%price_currency_code%' => $currency->getCode(),
                    ],
                    'ibexa_product_catalog'
                );

                return $this->redirectToProductView(
                    $product,
                    PricesTab::URI_FRAGMENT,
                    ['currency' => $currency->getCode()]
                );
            };
            $result = $this->submitHandler->handle($form, $handler);

            if ($result instanceof Response) {
                return $result;
            }
        }

        return new PriceUpdateView('@ibexadesign/product_catalog/product/price/edit.html.twig', $price, $form);
    }

    private function getUpdateForm(
        PriceInterface $price,
        ProductInterface $product,
        CurrencyInterface $currency
    ): FormInterface {
        $query = new CustomerGroupQuery();
        $query->setLimit(null);
        $customerGroupList = $this->customerGroupService->findCustomerGroups($query);
        $customerGroupPrices = [];
        foreach ($customerGroupList as $customerGroup) {
            $customerGroupPrice = $this->productPriceService->findOneForCustomerGroup($price, $customerGroup);
            $customerGroupPrices[] = $this->buildCustomerGroupPriceData(
                $customerGroupPrice,
                $customerGroup,
                $product,
                $currency,
            );
        }

        $priceData = new ProductPriceUpdateData($price);
        $data = new ProductPricesData($priceData, $customerGroupPrices);

        return $this->createForm(ProductPricesType::class, $data, [
            'currency' => $currency,
        ]);
    }

    private function buildCustomerGroupPriceData(
        ?CustomPriceAwareInterface $customerGroupPrice,
        CustomerGroupInterface $customerGroup,
        ProductInterface $product,
        CurrencyInterface $currency
    ): CustomerGroupAwareInterface {
        if ($customerGroupPrice === null) {
            return new CustomerGroupPriceCreateData($product, $currency, $customerGroup);
        }

        return new CustomerGroupPriceUpdateData($customerGroupPrice, $customerGroup);
    }
}
