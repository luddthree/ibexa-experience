<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\Common;

/**
 * Converts product operator to content operator.
 */
interface OperatorConverterInterface
{
    /**
     * @phpstan-return \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator::*
     */
    public function convert(string $operator): string;
}
