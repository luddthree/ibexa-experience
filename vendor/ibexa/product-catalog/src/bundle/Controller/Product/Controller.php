<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\Product;

use Ibexa\Contracts\AdminUi\Controller\Controller as BaseController;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

abstract class Controller extends BaseController
{
    /**
     * @param array<string,mixed> $parameters
     */
    public function redirectToProductView(
        ProductInterface $product,
        ?string $fragment = null,
        array $parameters = []
    ): RedirectResponse {
        return $this->redirectToRoute(
            'ibexa.product_catalog.product.view',
            [
                'productCode' => $product->getCode(),
                '_fragment' => $fragment,
            ] + $parameters
        );
    }

    public function redirectToProductList(): RedirectResponse
    {
        return $this->redirectToRoute('ibexa.product_catalog.product.list');
    }
}
