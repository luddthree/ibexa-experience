<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\AttributeDefinition;

use Ibexa\Bundle\ProductCatalog\View\AttributeDefinitionView;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Ibexa\Core\MVC\Symfony\Controller\Controller;

final class ViewController extends Controller
{
    public function renderAction(AttributeDefinitionInterface $attributeDefinition): AttributeDefinitionView
    {
        return new AttributeDefinitionView(
            '@ibexadesign/product_catalog/attribute_definition/view.html.twig',
            $attributeDefinition
        );
    }
}
