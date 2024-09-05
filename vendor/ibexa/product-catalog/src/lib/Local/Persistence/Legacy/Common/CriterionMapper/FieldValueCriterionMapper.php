<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Common\CriterionMapper;

use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\CriterionInterface;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion;

/**
 * @template-extends \Ibexa\ProductCatalog\Local\Persistence\Legacy\Common\CriterionMapper\AbstractFieldCriterionMapper<
 *     \Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion
 * >
 */
final class FieldValueCriterionMapper extends AbstractFieldCriterionMapper
{
    public function canHandle(CriterionInterface $criterion): bool
    {
        return $criterion instanceof FieldValueCriterion;
    }
}
