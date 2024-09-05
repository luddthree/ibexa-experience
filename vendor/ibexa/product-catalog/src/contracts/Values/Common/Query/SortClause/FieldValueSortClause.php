<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Common\Query\SortClause;

use Ibexa\Contracts\ProductCatalog\Values\Common\Query\AbstractSortClause;

class FieldValueSortClause extends AbstractSortClause
{
    private string $field;

    public function __construct(string $field, string $sortDirection = self::SORT_ASC)
    {
        parent::__construct($sortDirection);

        $this->field = $field;
    }

    public function getField(): string
    {
        return $this->field;
    }
}
