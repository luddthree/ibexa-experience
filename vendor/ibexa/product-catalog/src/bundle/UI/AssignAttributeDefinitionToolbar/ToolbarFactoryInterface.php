<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\UI\AssignAttributeDefinitionToolbar;

use Ibexa\Bundle\ProductCatalog\UI\AssignAttributeDefinitionToolbar\Values\Toolbar;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;

interface ToolbarFactoryInterface
{
    public function create(?ProductTypeInterface $productType = null): Toolbar;
}
