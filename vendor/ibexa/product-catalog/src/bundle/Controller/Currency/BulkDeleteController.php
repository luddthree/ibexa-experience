<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\Currency;

use Exception;
use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Bundle\ProductCatalog\Form\Data\Currency\CurrencyDeleteData;
use Ibexa\Bundle\ProductCatalog\Form\Type\Currency\CurrencyDeleteType;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Commerce\AdministrateCurrencies;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class BulkDeleteController extends Controller
{
    private Repository $repository;

    private CurrencyServiceInterface $currencyService;

    private FormFactoryInterface $formFactory;

    private SubmitHandler $submitHandler;

    private TranslatableNotificationHandlerInterface $notificationHandler;

    public function __construct(
        Repository $repository,
        CurrencyServiceInterface $currencyService,
        FormFactoryInterface $formFactory,
        SubmitHandler $submitHandler,
        TranslatableNotificationHandlerInterface $notificationHandler
    ) {
        $this->repository = $repository;
        $this->currencyService = $currencyService;
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
        $this->notificationHandler = $notificationHandler;
    }

    /**
     * @throws \Symfony\Component\HttpFoundation\Exception\BadRequestException
     */
    public function executeAction(Request $request): Response
    {
        $this->denyAccessUnlessGranted(new AdministrateCurrencies());

        $form = $this->createBulkDeleteForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle(
                $form,
                function (CurrencyDeleteData $data): ?Response {
                    $this->handleFormSubmit($data);

                    return null;
                }
            );

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirectToRoute('ibexa.product_catalog.currency.list');
    }

    private function createBulkDeleteForm(): FormInterface
    {
        return $this->formFactory->create(CurrencyDeleteType::class, null, [
            'method' => Request::METHOD_POST,
            'action' => $this->generateUrl('ibexa.product_catalog.currency.bulk_delete'),
        ]);
    }

    /**
     * @throws \Exception
     */
    private function handleFormSubmit(CurrencyDeleteData $data): void
    {
        $codes = [];
        $this->repository->beginTransaction();

        try {
            foreach ($data->getCurrencies() as $currency) {
                $this->currencyService->deleteCurrency($currency);
                $codes[] = $currency->getCode();
            }

            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }

        $this->notificationHandler->success(
            /** @Desc("{1}Currency '%deletedCodes%' deleted.|]1,Inf[ Currencies '%deletedCodes%' deleted.") */
            'currency.delete.success',
            [
                '%deletedCodes%' => implode("', '", $codes),
                '%count%' => count($codes),
            ],
            'ibexa_product_catalog'
        );
    }
}
