<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ColorAttribute as ColorAttributeCriterion;
use Ibexa\Rest\Value;

final class ColorAttribute extends Value
{
    public ColorAttributeCriterion $colorAttribute;

    public function __construct(ColorAttributeCriterion $colorAttribute)
    {
        $this->colorAttribute = $colorAttribute;
    }
}
