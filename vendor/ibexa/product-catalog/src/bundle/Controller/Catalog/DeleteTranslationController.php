<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\Catalog;

use Exception;
use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Bundle\ProductCatalog\Form\Data\Catalog\Translation\TranslationDeleteData;
use Ibexa\Bundle\ProductCatalog\Form\Type\Catalog\Translation\TranslationDeleteType;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\ProductCatalog\CatalogServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Catalog\CatalogDeleteTranslationStruct;
use Ibexa\Contracts\ProductCatalog\Values\CatalogInterface;
use Ibexa\ProductCatalog\Tab\Catalog\TranslationsTab;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DeleteTranslationController extends Controller
{
    private CatalogServiceInterface $catalogService;

    private FormFactoryInterface $formFactory;

    private SubmitHandler $submitHandler;

    private Repository $repository;

    private TranslatableNotificationHandlerInterface $notificationHandler;

    public function __construct(
        CatalogServiceInterface $catalogService,
        FormFactoryInterface $formFactory,
        SubmitHandler $submitHandler,
        Repository $repository,
        TranslatableNotificationHandlerInterface $notificationHandler
    ) {
        $this->catalogService = $catalogService;
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
        $this->repository = $repository;
        $this->notificationHandler = $notificationHandler;
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\CatalogInterface&\Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface $catalog
     */
    public function executeAction(CatalogInterface $catalog, Request $request): Response
    {
        $data = new TranslationDeleteData($catalog);
        $form = $this->formFactory->createNamed('delete-translations', TranslationDeleteType::class, $data);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (TranslationDeleteData $data): RedirectResponse {
                $catalog = $data->getCatalog();
                $languageCodes = $data->getLanguageCodes() ?? [];
                $catalogNames = [];
                $languageCodesToDelete = [];

                $this->repository->beginTransaction();
                try {
                    foreach ($languageCodes as $languageCode => $isChecked) {
                        $struct = new CatalogDeleteTranslationStruct($catalog, $languageCode);
                        $this->catalogService->deleteCatalogTranslation($struct);
                        $catalogNames[] = $catalog->getName();
                        $languageCodesToDelete[] = $languageCode;
                    }

                    $this->repository->commit();
                } catch (Exception $e) {
                    $this->repository->rollback();
                    throw $e;
                }

                $this->notificationHandler->success(
                    /** @Desc("{1}Translation in '%languageCodes%' of catalog '%deletedNames%' deleted.|]1,Inf[ Translations in '%languageCodes%' of catalog '%deletedNames%' (respectively) deleted.") */
                    'catalog.delete.translation.success',
                    [
                        '%languageCodes%' => implode("', '", $languageCodesToDelete),
                        '%deletedNames%' => implode("', '", $catalogNames),
                        '%count%' => count($catalogNames),
                    ],
                    'ibexa_product_catalog'
                );

                return $this->redirectToRoute(
                    'ibexa.product_catalog.catalog.view',
                    [
                        'catalogId' => $catalog->getId(),
                        '_fragment' => TranslationsTab::URI_FRAGMENT,
                    ]
                );
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirectToRoute('ibexa.product_catalog.catalog.view', [
            'catalogId' => $catalog->getId(),
            '_fragment' => TranslationsTab::URI_FRAGMENT,
        ]);
    }
}
