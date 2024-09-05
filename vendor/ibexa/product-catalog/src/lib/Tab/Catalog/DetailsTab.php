<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Tab\Catalog;

use Ibexa\ProductCatalog\Tab\AbstractDetailsTab;

class DetailsTab extends AbstractDetailsTab
{
    public const URI_FRAGMENT = 'ibexa-tab-catalog-details';

    public function getTemplate(): string
    {
        return '@ibexadesign/product_catalog/catalog/tab/details.html.twig';
    }

    public function getTemplateParameters(array $contextParameters = []): array
    {
        $viewParameters = [
            'catalog' => $contextParameters['catalog'],
        ];

        return array_replace($contextParameters, $viewParameters);
    }
}
