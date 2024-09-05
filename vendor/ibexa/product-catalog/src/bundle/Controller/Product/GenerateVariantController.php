<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\Product;

use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Bundle\ProductCatalog\Form\Data\ProductVariantGeneratorData;
use Ibexa\Bundle\ProductCatalog\Form\Type\ProductVariantGeneratorType;
use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductVariantGenerateStruct;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\ProductCatalog\Local\Repository\Variant\VariantGeneratorInterface;
use Ibexa\ProductCatalog\Tab\Product\VariantsTab;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class GenerateVariantController extends Controller
{
    private SubmitHandler $submitHandler;

    private VariantGeneratorInterface $variantGenerator;

    public function __construct(
        SubmitHandler $submitHandler,
        VariantGeneratorInterface $variantGenerator
    ) {
        $this->submitHandler = $submitHandler;
        $this->variantGenerator = $variantGenerator;
    }

    public function executeAction(Request $request, ProductInterface $product): Response
    {
        $form = $this->createForm(
            ProductVariantGeneratorType::class,
            null,
            [
                'product_type' => $product->getProductType(),
            ]
        );

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle(
                $form,
                function (ProductVariantGeneratorData $data) use ($product): ?Response {
                    $this->variantGenerator->generateVariants(
                        $product,
                        new ProductVariantGenerateStruct($data->getAttributes())
                    );

                    return null;
                }
            );

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirectToProductView($product, VariantsTab::URI_FRAGMENT);
    }
}
