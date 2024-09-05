<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\Query;

use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion;

final class AttributeGroupIdentifierCriterion extends FieldValueCriterion
{
    /**
     * @param string|string[] $value
     */
    public function __construct($value, ?string $operator = null)
    {
        parent::__construct(
            'attribute_group.identifier',
            $value,
            $operator,
        );
    }
}
