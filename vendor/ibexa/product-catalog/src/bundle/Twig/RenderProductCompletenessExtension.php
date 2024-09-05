<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class RenderProductCompletenessExtension extends AbstractExtension
{
    /**
     * @return \Twig\TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'ibexa_render_product_completeness',
                [RenderProductCompletenessRuntime::class, 'renderCompleteness'],
                ['is_safe' => ['html']]
            ),
        ];
    }
}
