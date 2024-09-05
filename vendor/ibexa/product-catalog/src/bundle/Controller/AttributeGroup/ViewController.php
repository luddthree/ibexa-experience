<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\AttributeGroup;

use Ibexa\Bundle\ProductCatalog\View\AttributeGroupView;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\AttributeGroup\View;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;

final class ViewController extends Controller
{
    public function renderAction(AttributeGroupInterface $attributeGroup): AttributeGroupView
    {
        $this->denyAccessUnlessGranted(new View());

        return new AttributeGroupView(
            '@ibexadesign/product_catalog/attribute_group/view.html.twig',
            $attributeGroup
        );
    }
}
