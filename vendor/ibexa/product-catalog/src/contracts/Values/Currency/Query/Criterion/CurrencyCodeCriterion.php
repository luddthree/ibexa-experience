<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Currency\Query\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion;

final class CurrencyCodeCriterion extends FieldValueCriterion
{
    public function __construct(string $code, ?string $operator = null)
    {
        parent::__construct('code', $code, $operator);
    }
}
