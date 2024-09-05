<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

final class RenderCatalogExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'ibexa_render_catalog_status',
                [RenderCatalogRuntime::class, 'renderCatalogStatus'],
                []
            ),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'ibexa_get_filter_preview_templates',
                [RenderCatalogRuntime::class, 'getFilterPreviewTemplates'],
                []
            ),
            new TwigFunction(
                'ibexa_get_product_catalog_root',
                [RenderCatalogRuntime::class, 'getProductCatalogRoot'],
                []
            ),
            new TwigFunction(
                'ibexa_is_pim_local',
                [RenderCatalogRuntime::class, 'isPimLocal'],
            ),
        ];
    }
}
