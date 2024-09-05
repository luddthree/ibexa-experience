<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\Product;

use Ibexa\Bundle\Core\Controller;
use Ibexa\Contracts\ProductCatalog\Local\LocalProductServiceInterface;
use Ibexa\Core\MVC\Symfony\View\ContentView;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class RedirectController extends Controller
{
    private LocalProductServiceInterface $productService;

    public function __construct(LocalProductServiceInterface $productService)
    {
        $this->productService = $productService;
    }

    public function execute(ContentView $view): RedirectResponse
    {
        $product = $this->productService->getProductFromContent($view->getContent());

        return $this->redirectToRoute(
            'ibexa.product_catalog.product.view',
            [
                'productCode' => $product->getCode(),
            ]
        );
    }
}
