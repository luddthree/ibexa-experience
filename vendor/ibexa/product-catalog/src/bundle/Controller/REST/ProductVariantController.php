<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\REST;

use Ibexa\Bundle\ProductCatalog\REST\Value\ProductVariant;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\ProductCatalog\Local\LocalProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductVariantCreateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductVariantGenerateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductVariantUpdateStruct;
use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\Rest\Exceptions\NotFoundException as RestNotFoundException;
use Ibexa\ProductCatalog\Local\Repository\Variant\VariantGeneratorInterface;
use Ibexa\Rest\Message;
use Ibexa\Rest\Server\Controller as RestController;
use Ibexa\Rest\Server\Values\NoContent;
use Ibexa\Rest\Value;
use Symfony\Component\HttpFoundation\Request;

final class ProductVariantController extends RestController
{
    private ProductServiceInterface $productService;

    private LocalProductServiceInterface $localProductService;

    private VariantGeneratorInterface $variantGenerator;

    public function __construct(
        ProductServiceInterface $productService,
        LocalProductServiceInterface $localProductService,
        VariantGeneratorInterface $variantGenerator
    ) {
        $this->productService = $productService;
        $this->localProductService = $localProductService;
        $this->variantGenerator = $variantGenerator;
    }

    public function getProductVariantAction(Request $request, string $code): Value
    {
        return new ProductVariant(
            $this->productService->getProductVariant($code)
        );
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function createProductVariantAction(Request $request, string $baseProductCode): Value
    {
        /** @var \Ibexa\Bundle\ProductCatalog\REST\Value\ProductVariantCreateStruct $input */
        $input = $this->inputDispatcher->parse(
            new Message(
                ['Content-Type' => $request->headers->get('Content-Type')],
                $request->getContent()
            )
        );

        try {
            $product = $this->productService->getProduct($baseProductCode);
        } catch (NotFoundException $e) {
            throw new RestNotFoundException($e->getMessage(), 0, $e);
        }

        $productVariantCreateStruct = new ProductVariantCreateStruct(
            $input->getAttributes(),
            $input->getCode()
        );

        $this->localProductService->createProductVariants(
            $product,
            [$productVariantCreateStruct]
        );

        return new NoContent();
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Rest\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function generateProductVariantsAction(Request $request, string $baseProductCode): Value
    {
        /** @var \Ibexa\Bundle\ProductCatalog\REST\Value\ProductVariantsGenerateStruct $input */
        $input = $this->inputDispatcher->parse(
            new Message(
                ['Content-Type' => $request->headers->get('Content-Type')],
                $request->getContent()
            )
        );

        try {
            $product = $this->productService->getProduct($baseProductCode);
        } catch (NotFoundException $e) {
            throw new RestNotFoundException($e->getMessage(), 0, $e);
        }

        $this->variantGenerator->generateVariants(
            $product,
            new ProductVariantGenerateStruct($input->getAttributes())
        );

        return new NoContent();
    }

    /**
     * @throws \Ibexa\Contracts\Rest\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function updateProductVariantAction(Request $request, string $code): Value
    {
        /** @var \Ibexa\Bundle\ProductCatalog\REST\Value\ProductVariantUpdateStruct $input */
        $input = $this->inputDispatcher->parse(
            new Message(
                ['Content-Type' => $request->headers->get('Content-Type')],
                $request->getContent()
            )
        );

        try {
            $productVariant = $this->productService->getProductVariant($code);

            $productVariantUpdateStruct = new ProductVariantUpdateStruct(
                $input->getAttributes(),
                $input->getCode()
            );

            return new ProductVariant(
                $this->localProductService->updateProductVariant(
                    $productVariant,
                    $productVariantUpdateStruct
                )
            );
        } catch (NotFoundException $e) {
            throw new RestNotFoundException($e->getMessage(), 0, $e);
        }
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function deleteProductVariantAction(string $code): Value
    {
        $productVariant = $this->productService->getProduct($code);
        $this->localProductService->deleteProduct($productVariant);

        return new NoContent();
    }
}
