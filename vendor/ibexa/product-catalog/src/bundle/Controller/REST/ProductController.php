<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\REST;

use Ibexa\Bundle\ProductCatalog\REST\Value\Product;
use Ibexa\Bundle\ProductCatalog\REST\Value\ProductList;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\ProductCatalog\Local\LocalProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductCreateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductUpdateStruct;
use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\ProductTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeInterface;
use Ibexa\Contracts\ProductCatalog\Values\LanguageSettings;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery;
use Ibexa\Contracts\Rest\Exceptions\NotFoundException as RestNotFoundException;
use Ibexa\Rest\Message;
use Ibexa\Rest\Server\Controller as RestController;
use Ibexa\Rest\Server\Values\NoContent;
use Ibexa\Rest\Value;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class ProductController extends RestController
{
    private ProductServiceInterface $productService;

    private LocalProductServiceInterface $localProductService;

    private ProductTypeServiceInterface $productTypeService;

    private DenormalizerInterface $denormalizer;

    public function __construct(
        ProductServiceInterface $productService,
        LocalProductServiceInterface $localProductService,
        ProductTypeServiceInterface $productTypeService,
        DenormalizerInterface $denormalizer
    ) {
        $this->productService = $productService;
        $this->localProductService = $localProductService;
        $this->productTypeService = $productTypeService;
        $this->denormalizer = $denormalizer;
    }

    /**
     * @deprecated since 4.3. Use ibexa.product_catalog.rest.products.view route instead.
     */
    public function listProductsAction(Request $request): Value
    {
        trigger_deprecation(
            'ibexa/product-catalog',
            '4.3',
            sprintf(
                '%s route is deprecated and will be removed in 5.0. Use %s route instead.',
                'ibexa.product_catalog.rest.products',
                'ibexa.product_catalog.rest.products.view'
            ),
        );

        $restProducts = [];

        /** @var \Ibexa\Bundle\ProductCatalog\REST\Value\ProductQueryStruct $input */
        $input = $this->inputDispatcher->parse(
            new Message(
                ['Content-Type' => $request->headers->get('Content-Type')],
                $request->getContent()
            )
        );

        $productQuery = new ProductQuery(
            null,
            null,
            [],
            $input->getOffset(),
            $input->getLimit()
        );

        $languageSettings = new LanguageSettings($input->getLanguages());

        $products = $this->productService->findProducts($productQuery, $languageSettings);

        foreach ($products as $product) {
            $restProducts[] = new Product($product);
        }

        return new ProductList($restProducts);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function getProductAction(Request $request, string $code): Value
    {
        $languageSettings = null;
        $requestContent = $request->getContent();

        if (!empty($requestContent)) {
            /** @var \Ibexa\Bundle\ProductCatalog\REST\Value\ProductLanguageStruct $input */
            $input = $this->inputDispatcher->parse(
                new Message(
                    ['Content-Type' => $request->headers->get('Content-Type')],
                    $requestContent
                )
            );

            $languageSettings = new LanguageSettings($input->getLanguages());
        }

        $product = $this->productService->getProduct($code, $languageSettings);

        return new Product($product);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function createProductAction(
        Request $request,
        string $productTypeIdentifier
    ): Value {
        try {
            $productType = $this->productTypeService->getProductType($productTypeIdentifier);
        } catch (NotFoundException $e) {
            throw new RestNotFoundException($e->getMessage(), 0, $e);
        }

        /** @var \Ibexa\Bundle\ProductCatalog\REST\Value\ProductCreateStruct $input */
        $input = $this->inputDispatcher->parse(
            new Message(
                ['Content-Type' => $request->headers->get('Content-Type')],
                $request->getContent()
            )
        );

        $attributes = $this->denormalizer->denormalize(
            $input->getAttributes(),
            AttributeInterface::class . '[]',
            null,
            [
                'product_type' => $productType,
            ],
        );

        $productCreateStruct = new ProductCreateStruct(
            $productType,
            $input->getContentCreateStruct()->contentCreateStruct,
            $input->getCode(),
        );

        $productCreateStruct->setAttributes($attributes);
        $product = $this->localProductService->createProduct($productCreateStruct);

        return new Product($product);
    }

    /**
     * @throws \Ibexa\Contracts\Rest\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function updateProductAction(Request $request, string $code): Value
    {
        /** @var \Ibexa\Bundle\ProductCatalog\REST\Value\ProductUpdateStruct $input */
        $input = $this->inputDispatcher->parse(
            new Message(
                ['Content-Type' => $request->headers->get('Content-Type')],
                $request->getContent()
            )
        );

        try {
            $product = $this->productService->getProduct($code);
        } catch (NotFoundException $e) {
            throw new RestNotFoundException($e->getMessage(), 0, $e);
        }

        $productUpdateStruct = new ProductUpdateStruct(
            $product,
            $input->getContentUpdateStruct()
        );

        $attributes = $this->denormalizer->denormalize(
            $input->getAttributes(),
            AttributeInterface::class . '[]',
            null,
            [
                'product_type' => $product->getProductType(),
            ],
        );

        $productUpdateStruct->setCode($input->getCode());
        $productUpdateStruct->setAttributes($attributes);

        $product = $this->localProductService->updateProduct($productUpdateStruct);

        return new Product($product);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function deleteProductAction(string $identifier): Value
    {
        $product = $this->productService->getProduct($identifier);
        $this->localProductService->deleteProduct($product);

        return new NoContent();
    }
}
