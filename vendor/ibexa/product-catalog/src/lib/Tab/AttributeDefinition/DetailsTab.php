<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Tab\AttributeDefinition;

use Ibexa\ProductCatalog\Tab\AbstractDetailsTab;

class DetailsTab extends AbstractDetailsTab
{
    public const URI_FRAGMENT = 'ibexa-tab-attribute-definition-details';

    public function getTemplate(): string
    {
        return '@ibexadesign/product_catalog/attribute_definition/tab/details.html.twig';
    }

    public function getTemplateParameters(array $contextParameters = []): array
    {
        $viewParameters = [
            'attribute_definition' => $contextParameters['attribute_definition'],
        ];

        return array_replace($contextParameters, $viewParameters);
    }
}
