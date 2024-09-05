<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query;

final class AttributeGroupViewInput extends AbstractViewInput
{
    protected function getViewInputIdentifier(): string
    {
        return 'AttributeGroupQuery';
    }
}
