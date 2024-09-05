<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Common\CriterionMapper\Price;

use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\CriterionInterface;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\Query\Criterion\CustomerGroup;
use Ibexa\Core\Base\Exceptions\InvalidArgumentType;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\Common\CriterionMapper\AbstractFieldCriterionMapper;

/**
 * @template-extends \Ibexa\ProductCatalog\Local\Persistence\Legacy\Common\CriterionMapper\AbstractFieldCriterionMapper<
 *     \Ibexa\Contracts\ProductCatalog\Values\Price\Query\Criterion\CustomerGroup
 * >
 */
final class CustomerGroupCriterionMapper extends AbstractFieldCriterionMapper
{
    public function canHandle(CriterionInterface $criterion): bool
    {
        return $criterion instanceof CustomerGroup;
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\Price\Query\Criterion\CustomerGroup $criterion
     *
     * @return int|int[]
     */
    protected function getComparisonValue(FieldValueCriterion $criterion)
    {
        $value = $criterion->getValue();

        if ($value instanceof CustomerGroupInterface) {
            return $value->getId();
        }

        if (is_array($value)) {
            return array_map(
                static fn (CustomerGroupInterface $customerGroup): int => $customerGroup->getId(),
                $value
            );
        }

        throw new InvalidArgumentType(
            '$criterion->value',
            CustomerGroupInterface::class . '|' . CustomerGroupInterface::class . '[]'
        );
    }
}
