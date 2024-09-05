<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\Product;

use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Bundle\ProductCatalog\Form\Data\ProductTranslationAddData;
use Ibexa\Bundle\ProductCatalog\Form\Type\ProductTranslationAddType;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\ProductCatalog\Tab\Product\TranslationsTab;
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

    public function executeAction(Request $request, ProductInterface $product): Response
    {
        $form = $this->createCreateTranslationForm($product);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle(
                $form,
                function (ProductTranslationAddData $data) use ($product): RedirectResponse {
                    return $this->handleFormSubmission($product, $data);
                }
            );

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirectToProductView($product, TranslationsTab::URI_FRAGMENT);
    }

    private function createCreateTranslationForm(ProductInterface $product): FormInterface
    {
        return $this->formFactory->createNamed(
            'add-translation',
            ProductTranslationAddType::class,
            null,
            [
                'product' => $product,
            ]
        );
    }

    private function handleFormSubmission(
        ProductInterface $product,
        ProductTranslationAddData $data
    ): RedirectResponse {
        return $this->redirectToRoute('ibexa.product_catalog.product.edit', [
            'productCode' => $product->getCode(),
            'languageCode' => $data->getLanguage()->languageCode,
            'baseLanguageCode' => $data->getBaseLanguage()->languageCode ?? null,
        ]);
    }
}
