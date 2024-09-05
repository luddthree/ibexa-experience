<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\Product;

use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Bundle\ProductCatalog\Form\Data\ProductVariantUpdateData;
use Ibexa\Bundle\ProductCatalog\Form\Type\ProductVariantUpdateType;
use Ibexa\Bundle\ProductCatalog\View\ProductVariantUpdateView;
use Ibexa\Contracts\ProductCatalog\Local\LocalProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductVariantUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionAssignmentInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class UpdateVariantController extends Controller
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
     * @return \Ibexa\Bundle\ProductCatalog\View\ProductVariantUpdateView|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentType
     */
    public function executeAction(Request $request, ProductVariantInterface $variant)
    {
        $form = $this->createForm(
            ProductVariantUpdateType::class,
            new ProductVariantUpdateData($variant),
        );

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $handler = function (ProductVariantUpdateData $data) use ($variant) {
                $updateStruct = new ProductVariantUpdateStruct();
                $updateStruct->setCode($data->getCode());
                $productTypeAttributesIdentifiers = array_map(
                    static function (AttributeDefinitionAssignmentInterface $attributeDefinitionAssignment): string {
                        return $attributeDefinitionAssignment->getAttributeDefinition()->getIdentifier();
                    },
                    (array) $variant->getProductType()->getAttributesDefinitions()
                );
                foreach ($data->getAttributes() as $identifier => $attribute) {
                    if (!in_array($identifier, $productTypeAttributesIdentifiers, true)) {
                        continue;
                    }

                    $updateStruct->setAttribute($identifier, $attribute->getValue());
                }

                $variant = $this->productService->updateProductVariant($variant, $updateStruct);

                return $this->redirectToProductView($variant);
            };

            $result = $this->submitHandler->handle($form, $handler);
            if ($result instanceof Response) {
                return $result;
            }
        }

        return new ProductVariantUpdateView(
            '@ibexadesign/product_catalog/product/variant_update.html.twig',
            $variant,
            $form
        );
    }
}
