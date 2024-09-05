<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value\Criterion\Mapper;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;
use Ibexa\Rest\Value;

interface CriterionMapperInterface
{
    public function mapToRest(CriterionInterface $criterion): Value;

    public function supports(CriterionInterface $criterion): bool;
}
