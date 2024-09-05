<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\Product;

use Ibexa\Bundle\ProductCatalog\Form\Data\ProductUpdateData;
use Ibexa\Bundle\ProductCatalog\Form\DataMapper\ProductUpdateMapper;
use Ibexa\Bundle\ProductCatalog\Form\Type\ProductUpdateType;
use Ibexa\Bundle\ProductCatalog\View\ProductUpdateView;
use Ibexa\ContentForms\Form\ActionDispatcher\ActionDispatcherInterface;
use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Product\PreEdit;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\Product;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

final class UpdateController extends Controller
{
    private LanguageService $languageService;

    private ActionDispatcherInterface $productActionDispatcher;

    public function __construct(
        LanguageService $languageService,
        ActionDispatcherInterface $productActionDispatcher
    ) {
        $this->languageService = $languageService;
        $this->productActionDispatcher = $productActionDispatcher;
    }

    /**
     * @return \Ibexa\Bundle\ProductCatalog\View\ProductUpdateView|\Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(
        Request $request,
        ProductInterface $product,
        ?string $languageCode = null,
        ?string $baseLanguageCode = null
    ) {
        if (!($product instanceof Product)) {
            return $this->redirectToRoute('ibexa.product_catalog.product.list');
        }

        $this->denyAccessUnlessGranted(new PreEdit($product));

        $languageCode ??= $product->getContent()->contentInfo->mainLanguageCode;
        $language = $this->languageService->loadLanguage($languageCode);

        $baseLanguage = null;
        if ($baseLanguageCode !== null) {
            $baseLanguage = $this->languageService->loadLanguage($baseLanguageCode);
        }

        $data = $this->createProductFormData($product, $language, $baseLanguage);
        $form = $this->createProductUpdateForm($product, $data, $language);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->productActionDispatcher->dispatchFormAction($form, $form->getData(), 'update');
            if ($response = $this->productActionDispatcher->getResponse()) {
                return $response;
            }
        }

        return new ProductUpdateView('@ibexadesign/product_catalog/product/edit.html.twig', $product, $language, $form);
    }

    private function createProductFormData(
        Product $product,
        ?Language $language,
        ?Language $baseLanguage
    ): ProductUpdateData {
        $mapper = new ProductUpdateMapper();
        $params = [
            'language' => $language,
            'baseLanguage' => $baseLanguage,
        ];

        return $mapper->mapToFormData($product, $params);
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\ProductInterface&\Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface $product
     */
    private function createProductUpdateForm(
        ProductInterface $product,
        ProductUpdateData $data,
        Language $language
    ): FormInterface {
        return $this->createForm(ProductUpdateType::class, $data, [
            'languageCode' => $language->languageCode,
            'mainLanguageCode' => $product->getContent()->contentInfo->mainLanguageCode,
        ]);
    }
}
