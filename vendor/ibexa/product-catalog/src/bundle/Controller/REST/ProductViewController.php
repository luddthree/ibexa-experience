<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\REST;

use Ibexa\Bundle\ProductCatalog\REST\Value\ProductView;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\LanguageSettings;
use Ibexa\Rest\Message;
use Ibexa\Rest\Server\Controller as RestController;
use Ibexa\Rest\Value;
use Symfony\Component\HttpFoundation\Request;

final class ProductViewController extends RestController
{
    private ProductServiceInterface $productService;

    public function __construct(ProductServiceInterface $productService)
    {
        $this->productService = $productService;
    }

    public function createView(Request $request): Value
    {
        $viewInput = $this->inputDispatcher->parse(
            new Message(
                ['Content-Type' => $request->headers->get('Content-Type')],
                $request->getContent()
            )
        );

        $languageFilter = new LanguageSettings(
            null !== $viewInput->languageCode ? [$viewInput->languageCode] : Language::ALL,
            $viewInput->useAlwaysAvailable ?? true,
        );

        return new ProductView(
            $viewInput->identifier,
            $this->productService->findProducts(
                $viewInput->query,
                $languageFilter
            )
        );
    }
}
