<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\Product;

use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Bundle\ProductCatalog\Form\Data\ProductVariantCreateData;
use Ibexa\Bundle\ProductCatalog\Form\Type\ProductVariantCreateType;
use Ibexa\Bundle\ProductCatalog\View\ProductVariantCreateView;
use Ibexa\Contracts\ProductCatalog\Local\LocalProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductVariantCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CreateVariantController extends Controller
{
    private LocalProductServiceInterface $productService;

    private SubmitHandler $submitHandler;

    public function __construct(
        LocalProductServiceInterface $productService,
        SubmitHandler $submitHandler
    ) {
        $this->productService = $productService;
        $this->submitHandler = $submitHandler;
    }

    /**
     * @return \Ibexa\Bundle\ProductCatalog\View\ProductVariantCreateView|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentType
     */
    public function executeAction(Request $request, ProductInterface $product)
    {
        $form = $this->createForm(
            ProductVariantCreateType::class,
            new ProductVariantCreateData($product),
        );

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $handler = function (ProductVariantCreateData $data) use ($product) {
                $attributes = [];
                $code = $data->getCode();
                foreach ($data->getAttributes() as $identifier => $attribute) {
                    $attributes[$identifier] = $attribute->getValue();
                }

                $this->productService->createProductVariants(
                    $product,
                    [new ProductVariantCreateStruct($attributes, $code)]
                );

                return $this->redirectToProductView($this->productService->getProduct($data->getCode()));
            };

            $result = $this->submitHandler->handle($form, $handler);
            if ($result instanceof Response) {
                return $result;
            }
        }

        return new ProductVariantCreateView(
            '@ibexadesign/product_catalog/product/variant_create.html.twig',
            $product,
            $form
        );
    }
}
