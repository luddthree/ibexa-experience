<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Catalog\Query\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion;

final class CatalogIdentifier extends FieldValueCriterion
{
    /**
     * @param string|array<string> $identifier
     */
    public function __construct($identifier)
    {
        $operator = FieldValueCriterion::COMPARISON_EQ;
        if (is_array($identifier)) {
            $operator = FieldValueCriterion::COMPARISON_IN;
        }

        parent::__construct('identifier', $identifier, $operator);
    }
}
