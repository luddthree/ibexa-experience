<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\Currency;

use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Bundle\ProductCatalog\Form\Data\Currency\CurrencyCreateData;
use Ibexa\Bundle\ProductCatalog\Form\Type\Currency\CurrencyCreateType;
use Ibexa\Bundle\ProductCatalog\View\CurrencyCreateView;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Commerce\AdministrateCurrencies;
use Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyCreateStruct;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CreateController extends AbstractController
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
     * @return \Ibexa\Bundle\ProductCatalog\View\CurrencyCreateView|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentType
     */
    public function renderAction(Request $request)
    {
        $this->denyAccessUnlessGranted(new AdministrateCurrencies());

        $data = new CurrencyCreateData();
        $form = $this->createForm(CurrencyCreateType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $handler = function (CurrencyCreateData $createData): RedirectResponse {
                assert(!empty($createData->getCode()));
                assert($createData->getSubunits() !== null);
                assert($createData->getEnabled() !== null);

                $struct = new CurrencyCreateStruct(
                    $createData->getCode(),
                    $createData->getSubunits(),
                    $createData->getEnabled(),
                );
                $currency = $this->currencyService->createCurrency($struct);

                $this->notificationHandler->success(
                    /** @Desc("Currency %currency_code% created.") */
                    'product.currency.create.success',
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

        return new CurrencyCreateView('@ibexadesign/product_catalog/currency/create.html.twig', $form);
    }
}
