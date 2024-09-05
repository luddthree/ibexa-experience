<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\ProductType;

use Exception;
use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Bundle\ProductCatalog\Form\Data\ProductTypeTranslationDeleteData;
use Ibexa\Bundle\ProductCatalog\Form\Type\ProductTypeTranslationDeleteType;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\ProductCatalog\Local\LocalProductTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\ProductType\Edit;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\ProductCatalog\Tab\ProductType\TranslationTab;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DeleteTranslationController extends Controller
{
    private Repository $repository;

    private LocalProductTypeServiceInterface $productTypeService;

    private FormFactoryInterface $formFactory;

    private SubmitHandler $submitHandler;

    public function __construct(
        Repository $repository,
        LocalProductTypeServiceInterface $productTypeService,
        FormFactoryInterface $formFactory,
        SubmitHandler $submitHandler
    ) {
        $this->repository = $repository;
        $this->productTypeService = $productTypeService;
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
    }

    public function executeAction(Request $request, ProductTypeInterface $productType): Response
    {
        $this->denyAccessUnlessGranted(new Edit());

        $form = $this->createTranslationDeleteForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle(
                $form,
                function (ProductTypeTranslationDeleteData $data) use ($productType): ?Response {
                    $this->handleFormSubmission($productType, $data);

                    return null;
                }
            );

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirectToRoute('ibexa.product_catalog.product_type.view', [
            'productTypeIdentifier' => $productType->getIdentifier(),
            '_fragment' => TranslationTab::URI_FRAGMENT,
        ]);
    }

    private function createTranslationDeleteForm(): FormInterface
    {
        return $this->formFactory->createNamed(
            'delete-translations',
            ProductTypeTranslationDeleteType::class,
            null,
            [
                'method' => Request::METHOD_POST,
            ]
        );
    }

    private function handleFormSubmission(
        ProductTypeInterface $productType,
        ProductTypeTranslationDeleteData $data
    ): void {
        $this->repository->beginTransaction();
        try {
            foreach (array_keys($data->getLanguageCodes() ?? []) as $languageCode) {
                $this->productTypeService->deleteProductTypeTranslation($productType, $languageCode);
            }

            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }
    }
}
