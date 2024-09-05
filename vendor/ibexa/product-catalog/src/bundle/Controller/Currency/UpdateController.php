<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\Currency;

use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Bundle\ProductCatalog\Form\Data\Currency\CurrencyUpdateData;
use Ibexa\Bundle\ProductCatalog\Form\Type\Currency\CurrencyUpdateType;
use Ibexa\Bundle\ProductCatalog\View\CurrencyUpdateView;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Commerce\AdministrateCurrencies;
use Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyUpdateStruct;
use Ibexa\ProductCatalog\Local\Repository\Values\Currency;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class UpdateController extends AbstractController
{
    private TranslatableNotificationHandlerInterface $notificationHandler;

    private SubmitHandler $submitHandler;

    private CurrencyServiceInterface $currencyService;

    public function __construct(
        TranslatableNotificationHandlerInterface $notificationHandler,
        SubmitHandler $submitHandler,
        CurrencyServiceInterface $currencyService
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->submitHandler = $submitHandler;
        $this->currencyService = $currencyService;
    }

    /**
     * @return \Ibexa\Bundle\ProductCatalog\View\CurrencyUpdateView|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentType
     */
    public function renderAction(Request $request, Currency $currency)
    {
        $this->denyAccessUnlessGranted(new AdministrateCurrencies());

        $currencyUpdateData = CurrencyUpdateData::createFromCurrency($currency);
        $form = $this->createForm(CurrencyUpdateType::class, $currencyUpdateData);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $handler = function (CurrencyUpdateData $updateData) use ($currency): RedirectResponse {
                assert(!empty($updateData->getCode()));

                $struct = new CurrencyUpdateStruct();
                $struct->setCode($updateData->getCode());
                $struct->setSubunits($updateData->getSubunits());
                $struct->setEnabled($updateData->getEnabled());
                $currency = $this->currencyService->updateCurrency($currency, $struct);

                $this->notificationHandler->success(
                    /** @Desc("Currency %currency_code% updated.") */
                    'product.currency.update.success',
                    [
                        '%currency_code%' => $currency->getCode(),
                    ],
                    'ibexa_product_catalog'
                );

                return $this->redirectToRoute('ibexa.product_catalog.currency.list');
            };
            $result = $this->submitHandler->handle($form, $handler);

            if ($result instanceof Response) {
                return $result;
            }
        }

        return new CurrencyUpdateView('@ibexadesign/product_catalog/currency/edit.html.twig', $currency, $form);
    }
}
