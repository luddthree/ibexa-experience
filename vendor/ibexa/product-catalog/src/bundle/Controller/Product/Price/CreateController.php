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
use Ibexa\Bundle\ProductCatalog\Form\Data\Price\Create\ProductPriceCreateData;
use Ibexa\Bundle\ProductCatalog\Form\Data\Price\ProductPricesData;
use Ibexa\Bundle\ProductCatalog\Form\DataMapper\ProductPrice\ProductPriceMapper;
use Ibexa\Bundle\ProductCatalog\Form\Type\ProductPricesType;
use Ibexa\Bundle\ProductCatalog\View\PriceCreateView;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\Core\Persistence\TransactionHandler;
use Ibexa\Contracts\ProductCatalog\CustomerGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Product\PreEdit;
use Ibexa\Contracts\ProductCatalog\ProductPriceServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupQuery;
use Ibexa\Contracts\ProductCatalog\Values\Price\Create\Struct\ProductPriceCreateStructInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\ProductCatalog\Tab\Product\PricesTab;
use JMS\TranslationBundle\Annotation\Desc;
use LogicException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class CreateController extends Controller
{
    private TransactionHandler $transactionHandler;

    private CustomerGroupServiceInterface $customerGroupService;

    private TranslatableNotificationHandlerInterface $notificationHandler;

    private SubmitHandler $submitHandler;

    private ProductPriceServiceInterface $productPriceService;

    private ProductPriceMapper $productPriceMapper;

    public function __construct(
        TransactionHandler $transactionHandler,
        TranslatableNotificationHandlerInterface $notificationHandler,
        SubmitHandler $submitHandler,
        ProductPriceServiceInterface $productPriceService,
        CustomerGroupServiceInterface $customerGroupService,
        ProductPriceMapper $productPriceMapper
    ) {
        $this->transactionHandler = $transactionHandler;
        $this->notificationHandler = $notificationHandler;
        $this->submitHandler = $submitHandler;
        $this->productPriceService = $productPriceService;
        $this->customerGroupService = $customerGroupService;
        $this->productPriceMapper = $productPriceMapper;
    }

    /**
     * @return \Ibexa\Bundle\ProductCatalog\View\PriceCreateView|\Symfony\Component\HttpFoundation\Response
     */
    public function renderAction(ProductInterface $product, CurrencyInterface $currency, Request $request)
    {
        $this->denyAccessUnlessGranted(new PreEdit($product));

        $form = $this->getCreateForm($product, $currency);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $handler = function (ProductPricesData $data) use ($product, $currency): RedirectResponse {
                $this->transactionHandler->beginTransaction();

                try {
                    foreach ($data as $priceChangeData) {
                        $struct = $this->productPriceMapper->mapToStruct($priceChangeData);
                        if (!$struct instanceof ProductPriceCreateStructInterface) {
                            throw new LogicException(sprintf(
                                'Expected %s, received %s. Unable to create price.',
                                ProductPriceCreateStructInterface::class,
                                get_class($struct),
                            ));
                        }

                        $this->productPriceService->createProductPrice($struct);
                    }

                    $this->transactionHandler->commit();
                    $this->notificationHandler->success(
                        /** @Desc("Prices in %price_currency_code% for Product '%product_name%' (%product_code%) created.") */
                        'product.price.create.success',
                        [
                            '%product_name%' => $product->getName(),
                            '%product_code%' => $product->getCode(),
                            '%price_currency_code%' => $currency->getCode(),
                        ],
                        'ibexa_product_catalog'
                    );
                } catch (Throwable $e) {
                    $this->transactionHandler->rollback();

                    throw $e;
                }

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

        return new PriceCreateView('@ibexadesign/product_catalog/product/price/create.html.twig', $product, $currency, $form);
    }

    private function getCreateForm(ProductInterface $product, CurrencyInterface $currency): FormInterface
    {
        $query = new CustomerGroupQuery();
        $query->setLimit(null);
        $customerGroupList = $this->customerGroupService->findCustomerGroups($query);

        $customerGroupPrices = [];
        foreach ($customerGroupList as $customerGroup) {
            $customerGroupPrice = new CustomerGroupPriceCreateData($product, $currency, $customerGroup);
            $customerGroupPrices[] = $customerGroupPrice;
        }

        $mainPriceData = new ProductPriceCreateData($product, $currency);
        $data = new ProductPricesData($mainPriceData, $customerGroupPrices);

        return $this->createForm(ProductPricesType::class, $data, [
            'currency' => $currency,
        ]);
    }
}
