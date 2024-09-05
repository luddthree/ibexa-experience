<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\ActionDispatcher;

use Ibexa\Bundle\ProductCatalog\Form\Event\ProductFormEvents;
use Ibexa\ContentForms\Form\ActionDispatcher\AbstractActionDispatcher;

final class ProductActionDispatcher extends AbstractActionDispatcher
{
    protected function getActionEventBaseName(): string
    {
        return ProductFormEvents::BASE_NAME;
    }
}
