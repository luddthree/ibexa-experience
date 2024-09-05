<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\Common;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator as ContentOperator;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\Operator;
use LogicException;

final class OperatorConverter implements OperatorConverterInterface
{
    /**
     * @phpstan-var array<
     *     \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\Operator::*,
     *     \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator::*
     * >
     */
    private static array $operatorMap = [
        Operator::EQ => ContentOperator::EQ,
        Operator::LT => ContentOperator::LT,
        Operator::LTE => ContentOperator::LTE,
        Operator::GT => ContentOperator::GT,
        Operator::GTE => ContentOperator::GTE,
    ];

    public function convert(string $operator): string
    {
        if (isset(self::$operatorMap[$operator])) {
            return self::$operatorMap[$operator];
        }

        throw new LogicException(sprintf(
            'Unable to map %s operator to a valid content operator',
            $operator,
        ));
    }
}
