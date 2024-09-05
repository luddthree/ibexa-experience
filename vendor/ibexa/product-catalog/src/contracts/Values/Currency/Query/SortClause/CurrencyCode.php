<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Currency\Query\SortClause;

use Ibexa\Contracts\ProductCatalog\Values\Currency\Query\FieldValueSortClause;

final class CurrencyCode extends FieldValueSortClause
{
    public function __construct(string $sortDirection = self::SORT_ASC)
    {
        parent::__construct('code', $sortDirection);
    }
}
