<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\Product;

use Exception;
use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Bundle\ProductCatalog\Form\Data\ProductTranslationDeleteData;
use Ibexa\Bundle\ProductCatalog\Form\Type\ProductTranslationDeleteType;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\ProductCatalog\Local\LocalProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\ProductCatalog\Tab\Product\TranslationsTab;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DeleteTranslationController extends Controller
{
    private Repository $repository;

    private LanguageService $languageService;

    private LocalProductServiceInterface $productService;

    private FormFactoryInterface $formFactory;

    private SubmitHandler $submitHandler;

    private TranslatableNotificationHandlerInterface $notificationHandler;

    public function __construct(
        Repository $repository,
        LanguageService $languageService,
        LocalProductServiceInterface $productService,
        FormFactoryInterface $formFactory,
        SubmitHandler $submitHandler,
        TranslatableNotificationHandlerInterface $notificationHandler
    ) {
        $this->repository = $repository;
        $this->languageService = $languageService;
        $this->productService = $productService;
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
        $this->notificationHandler = $notificationHandler;
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\ProductInterface&\Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface $product
     */
    public function executeAction(Request $request, ProductInterface $product): Response
    {
        $form = $this->createTranslationDeleteForm($product);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle(
                $form,
                function (ProductTranslationDeleteData $data): ?Response {
                    $this->handleFormSubmission($data);

                    return null;
                }
            );

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirectToProductView($product, TranslationsTab::URI_FRAGMENT);
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\ProductInterface&\Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface $product
     */
    private function createTranslationDeleteForm(ProductInterface $product): FormInterface
    {
        return $this->formFactory->createNamed(
            'delete-translations',
            ProductTranslationDeleteType::class,
            new ProductTranslationDeleteData($product)
        );
    }

    private function handleFormSubmission(ProductTranslationDeleteData $data): void
    {
        $removedLanguageCodes = [];

        $this->repository->beginTransaction();
        try {
            $product = $data->getProduct();
            $languages = $this->languageService->loadLanguageListByCode(array_keys($data->getLanguageCodes() ?? []));
            foreach ($languages as $language) {
                $this->productService->deleteProductTranslation($product, $language);
                $removedLanguageCodes[] = $language->getLanguageCode();
            }

            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }

        $this->notificationHandler->success(
            /** @Desc("Translations in '%languageCodes%' of product '%name%' removed.") */
            'product.delete.translation.success',
            [
                '%languageCodes%' => implode("', '", $removedLanguageCodes),
                '%name%' => $product->getName(),
            ],
            'ibexa_product_catalog'
        );
    }
}
