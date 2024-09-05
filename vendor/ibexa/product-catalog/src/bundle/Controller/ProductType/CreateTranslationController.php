<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\ProductType;

use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Bundle\ProductCatalog\Form\Data\ProductTypeTranslationAddData;
use Ibexa\Bundle\ProductCatalog\Form\Type\ProductTypeTranslationAddType;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\ProductType\Edit;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\ProductCatalog\Tab\ProductType\TranslationTab;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CreateTranslationController extends Controller
{
    private FormFactoryInterface $formFactory;

    private SubmitHandler $submitHandler;

    public function __construct(
        FormFactoryInterface $formFactory,
        SubmitHandler $submitHandler
    ) {
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
    }

    public function executeAction(Request $request, ProductTypeInterface $productType): Response
    {
        $this->denyAccessUnlessGranted(new Edit());

        $form = $this->createCreateTranslationForm($productType);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle(
                $form,
                function (ProductTypeTranslationAddData $data) use ($productType): RedirectResponse {
                    return $this->handleFormSubmission($productType, $data);
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

    private function createCreateTranslationForm(ProductTypeInterface $productType): FormInterface
    {
        return $this->formFactory->createNamed(
            'add-translation',
            ProductTypeTranslationAddType::class,
            null,
            [
                'method' => Request::METHOD_POST,
                'product_type' => $productType,
            ]
        );
    }

    private function handleFormSubmission(
        ProductTypeInterface $productType,
        ProductTypeTranslationAddData $data
    ): RedirectResponse {
        return $this->redirectToRoute(
            'ibexa.product_catalog.product_type.edit',
            [
                'productTypeIdentifier' => $productType->getIdentifier(),
                'languageCode' => $data->getLanguage()->languageCode,
                'baseLanguageCode' => $data->getBaseLanguage()->languageCode ?? null,
            ]
        );
    }
}
