<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\ProductType;

use Ibexa\Bundle\ProductCatalog\View\ProductTypeView;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\ProductType\View;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;

final class ViewController extends Controller
{
    public function renderAction(ProductTypeInterface $productType): ProductTypeView
    {
        $this->denyAccessUnlessGranted(new View());

        return new ProductTypeView('@ibexadesign/product_catalog/product_type/view.html.twig', $productType);
    }
}
