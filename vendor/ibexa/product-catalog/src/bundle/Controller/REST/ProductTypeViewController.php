<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\REST;

use Ibexa\Bundle\ProductCatalog\REST\Value\ProductTypeView;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\ProductCatalog\ProductTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\LanguageSettings;
use Ibexa\Rest\Message;
use Ibexa\Rest\Server\Controller as RestController;
use Ibexa\Rest\Value;
use Symfony\Component\HttpFoundation\Request;

final class ProductTypeViewController extends RestController
{
    private ProductTypeServiceInterface $productTypeService;

    public function __construct(ProductTypeServiceInterface $productTypeService)
    {
        $this->productTypeService = $productTypeService;
    }

    public function createView(Request $request): Value
    {
        /** @var \Ibexa\Bundle\ProductCatalog\REST\Value\RestViewInput $viewInput */
        $viewInput = $this->inputDispatcher->parse(
            new Message(
                ['Content-Type' => $request->headers->get('Content-Type')],
                $request->getContent()
            )
        );

        $languageFilter = new LanguageSettings(
            null !== $viewInput->languageCode ? [$viewInput->languageCode] : Language::ALL
        );

        return new ProductTypeView(
            $viewInput->identifier,
            $this->productTypeService->findProductTypes(
                $viewInput->query,
                $languageFilter
            )
        );
    }
}
