<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ProductTypePermissionExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'ibexa_permission_delete_product_type',
                [
                    ProductTypePermissionRuntime::class,
                    'canDeleteProductType',
                ]
            ),
            new TwigFunction(
                'ibexa_permission_create_product_type',
                [
                    ProductTypePermissionRuntime::class,
                    'canCreateProductType',
                ]
            ),
            new TwigFunction(
                'ibexa_permission_edit_product_type',
                [
                    ProductTypePermissionRuntime::class,
                    'canEditProductType',
                ]
            ),
        ];
    }
}
