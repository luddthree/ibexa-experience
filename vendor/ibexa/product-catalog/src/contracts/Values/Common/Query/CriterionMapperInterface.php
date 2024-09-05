<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Common\Query;

use Doctrine\Common\Collections\Expr\Expression;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\CriterionInterface;
use Ibexa\ProductCatalog\Local\Repository\CriterionMapper;

/**
 * @template C of \Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\CriterionInterface
 */
interface CriterionMapperInterface
{
    public function canHandle(CriterionInterface $criterion): bool;

    /**
     * @param C $criterion
     */
    public function handle(CriterionInterface $criterion, CriterionMapper $mapper): Expression;
}
