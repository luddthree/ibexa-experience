<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\Query;

use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion;

final class NameCriterion extends FieldValueCriterion
{
    public function __construct($value, ?string $operator = null)
    {
        $value = mb_strtolower($value);
        parent::__construct('name_normalized', $value, $operator);
    }
}
