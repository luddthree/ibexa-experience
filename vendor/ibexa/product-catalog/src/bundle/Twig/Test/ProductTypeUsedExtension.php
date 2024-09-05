<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Twig\Test;

use Twig\Extension\AbstractExtension;
use Twig\TwigTest;

final class ProductTypeUsedExtension extends AbstractExtension
{
    public function getTests(): array
    {
        return [
            new TwigTest(
                'ibexa_product_type_used',
                [ProductTypeUsedRuntime::class, 'productTypeUsed']
            ),
        ];
    }
}
