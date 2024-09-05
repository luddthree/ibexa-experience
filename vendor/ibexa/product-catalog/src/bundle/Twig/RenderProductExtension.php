<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigTest;

final class RenderProductExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'ibexa_get_product',
                [RenderProductRuntime::class, 'getProduct']
            ),
            new TwigFilter(
                'ibexa_format_product_attribute',
                [RenderProductRuntime::class, 'formatAttributeValue'],
                []
            ),
        ];
    }

    public function getTests(): array
    {
        return [
            new TwigTest(
                'ibexa_product',
                [RenderProductRuntime::class, 'isProduct']
            ),
        ];
    }
}
