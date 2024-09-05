<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class ParseUrlExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('url_component', [$this, 'getUrlComponent']),
        ];
    }

    public function getUrlComponent(?string $url, string $component): ?string
    {
        if ($url === null) {
            return null;
        }

        $components = parse_url($url);
        if ($components === false) {
            return null;
        }

        return $components[$component] ?? null;
    }
}
