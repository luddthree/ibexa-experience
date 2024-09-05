<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\Product;

use Ibexa\Bundle\ProductCatalog\View\ProductView;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Product\View;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;

final class ViewController extends Controller
{
    /**
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentType
     */
    public function renderAction(ProductInterface $product): ProductView
    {
        $this->denyAccessUnlessGranted(new View($product), $product);

        return new ProductView('@ibexadesign/product_catalog/product/view.html.twig', $product);
    }
}
