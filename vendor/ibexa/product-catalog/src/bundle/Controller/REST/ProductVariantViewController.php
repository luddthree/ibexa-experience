<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\REST;

use Ibexa\Bundle\ProductCatalog\REST\Value\ProductVariantView;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\Rest\Exceptions\NotFoundException as RestNotFoundException;
use Ibexa\Rest\Message;
use Ibexa\Rest\Server\Controller as RestController;
use Ibexa\Rest\Value;
use Symfony\Component\HttpFoundation\Request;

final class ProductVariantViewController extends RestController
{
    private ProductServiceInterface $productService;

    public function __construct(ProductServiceInterface $productService)
    {
        $this->productService = $productService;
    }

    /**
     * @throws \Ibexa\Contracts\Rest\Exceptions\NotFoundException
     */
    public function createView(Request $request, string $baseProductCode): Value
    {
        $viewInput = $this->inputDispatcher->parse(
            new Message(
                ['Content-Type' => $request->headers->get('Content-Type')],
                $request->getContent()
            )
        );

        try {
            $product = $this->productService->getProduct($baseProductCode);
        } catch (NotFoundException|UnauthorizedException $e) {
            throw new RestNotFoundException($e->getMessage(), 0, $e);
        }

        return new ProductVariantView(
            $viewInput->identifier,
            $this->productService->findProductVariants(
                $product,
                $viewInput->query
            )
        );
    }
}
