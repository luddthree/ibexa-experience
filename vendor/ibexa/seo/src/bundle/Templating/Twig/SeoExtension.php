<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Seo\Templating\Twig;

use Ibexa\Bundle\Seo\Templating\Twig\Functions\SeoRenderer;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SeoExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'ibexa_seo',
                [SeoRenderer::class, 'renderSeoFields'],
                [
                    'is_safe' => ['html'],
                ],
            ),
            new TwigFunction(
                'ibexa_seo_is_empty',
                [SeoRenderer::class, 'isEmpty'],
            ),
            new TwigFunction(
                'ibexa_seo_preview',
                [SeoRenderer::class, 'previewSeoFields'],
                [
                    'is_safe' => ['html'],
                ],
            ),
        ];
    }
}
