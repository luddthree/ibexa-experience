<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\SelectionAttribute as SelectionAttributeCriterion;
use Ibexa\Rest\Value;

final class SelectionAttribute extends Value
{
    public SelectionAttributeCriterion $selectionAttribute;

    public function __construct(SelectionAttributeCriterion $selectionAttribute)
    {
        $this->selectionAttribute = $selectionAttribute;
    }
}
