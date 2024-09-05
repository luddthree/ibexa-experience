<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteContext\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class PreviewUrlExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'ibexa_location_preview_url',
                [PreviewUrlRuntime::class, 'getPreviewUrl']
            ),
        ];
    }
}
