<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Common\CriterionMapper;

use Doctrine\Common\Collections\Expr\CompositeExpression;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\LogicalOr;

final class LogicalOrCriterionMapper extends AbstractCompositeCriterionMapper
{
    protected function getHandledClass(): string
    {
        return LogicalOr::class;
    }

    protected function getType(): string
    {
        return CompositeExpression::TYPE_OR;
    }
}
