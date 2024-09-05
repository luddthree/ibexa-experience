<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Event;

final class ProductFormEvents
{
    public const BASE_NAME = 'product.edit';

    public const PRODUCT_CREATE = 'product.edit.create';

    public const PRODUCT_UPDATE = 'product.edit.update';
}
