<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\Product;

use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Bundle\ProductCatalog\Form\Data\ProductCreateData;
use Ibexa\Bundle\ProductCatalog\Form\Data\ProductCreateRedirectData;
use Ibexa\Bundle\ProductCatalog\Form\DataMapper\ProductCreateMapper;
use Ibexa\Bundle\ProductCatalog\Form\Type\ProductCreateRedirectType;
use Ibexa\Bundle\ProductCatalog\Form\Type\ProductCreateType;
use Ibexa\Bundle\ProductCatalog\View\ProductCreateView;
use Ibexa\ContentForms\Form\ActionDispatcher\ActionDispatcherInterface;
use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

final class CreateController extends Controller
{
    private LanguageService $languageService;

    private SubmitHandler $submitHandler;

    private ActionDispatcherInterface $productActionDispatcher;

    public function __construct(
        LanguageService $languageService,
        SubmitHandler $submitHandler,
        ActionDispatcherInterface $productActionDispatcher
    ) {
        $this->languageService = $languageService;
        $this->submitHandler = $submitHandler;
        $this->productActionDispatcher = $productActionDispatcher;
    }

    public function createProxyAction(Request $request): RedirectResponse
    {
        $form = $this->createCreateRedirectionForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (ProductCreateRedirectData $data): RedirectResponse {
                return $this->redirectToRoute(
                    'ibexa.product_catalog.product.create',
                    [
                        'productTypeIdentifier' => $data->getProductType()->getIdentifier(),
                        'languageCode' => $data->getLanguage()->languageCode,
                    ]
                );
            });

            if ($result instanceof RedirectResponse) {
                return $result;
            }
        }

        return $this->redirectToProductList();
    }

    /**
     * @return \Ibexa\Bundle\ProductCatalog\View\ProductCreateView|\Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request, ProductTypeInterface $productType, string $languageCode)
    {
        $language = $this->languageService->loadLanguage($languageCode);

        $data = $this->createProductFormData($productType, $language);
        $form = $this->createProductCreateForm($data, $language);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->productActionDispatcher->dispatchFormAction($form, $data, 'create');
            if ($response = $this->productActionDispatcher->getResponse()) {
                return $response;
            }
        }

        return new ProductCreateView('@ibexadesign/product_catalog/product/create.html.twig', $productType, $language, $form);
    }

    private function createProductFormData(ProductTypeInterface $productType, Language $language): ProductCreateData
    {
        $mapper = new ProductCreateMapper();
        $params = [
            'languageCode' => $language->languageCode,
        ];

        return $mapper->mapToFormData($productType, $params);
    }

    private function createProductCreateForm(ProductCreateData $data, Language $language): FormInterface
    {
        return $this->createForm(ProductCreateType::class, $data, [
            'languageCode' => $language->languageCode,
            'mainLanguageCode' => $language->languageCode,
        ]);
    }

    private function createCreateRedirectionForm(): FormInterface
    {
        return $this->createForm(ProductCreateRedirectType::class, null, [
            'action' => $this->generateUrl('ibexa.product_catalog.product.create_proxy'),
            'method' => Request::METHOD_POST,
        ]);
    }
}
