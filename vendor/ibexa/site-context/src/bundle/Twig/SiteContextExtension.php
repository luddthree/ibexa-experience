<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteContext\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\TwigTest;

final class SiteContextExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'ibexa_site_context_get_current',
                [SiteContextRuntime::class, 'getCurrentContext'],
            ),
            new TwigFunction(
                'ibexa_site_context_is_location_preview_enabled',
                [SiteContextRuntime::class, 'isLocationPreviewEnabled']
            ),
        ];
    }

    public function getTests(): array
    {
        return [
            new TwigTest(
                'ibexa_site_context_aware',
                [SiteContextRuntime::class, 'isLocationContextAware']
            ),
        ];
    }
}
