<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\AttributeGroup;

use Ibexa\Contracts\Migration\StepExecutor\AbstractStepExecutor;
use Ibexa\Contracts\ProductCatalog\Local\LocalAttributeGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use InvalidArgumentException;
use Webmozart\Assert\Assert;

/**
 * @internal
 */
abstract class AbstractAttributeGroupChangeStepExecutor extends AbstractStepExecutor
{
    protected LocalAttributeGroupServiceInterface $attributeGroupService;

    public function __construct(LocalAttributeGroupServiceInterface $attributeGroupService)
    {
        $this->attributeGroupService = $attributeGroupService;
    }

    /**
     * @param \Ibexa\ProductCatalog\Migrations\AttributeGroup\AttributeGroupDeleteStep|\Ibexa\ProductCatalog\Migrations\AttributeGroup\AttributeGroupUpdateStep $step
     */
    final protected function findAttributeGroup(StepInterface $step): AttributeGroupInterface
    {
        Assert::isInstanceOfAny($step, [AttributeGroupDeleteStep::class, AttributeGroupUpdateStep::class]);

        $criterion = $step->getCriterion();
        if (!$criterion instanceof FieldValueCriterion) {
            throw new InvalidArgumentException(sprintf(
                'Only %s is supported.',
                FieldValueCriterion::class,
            ));
        }

        if ($criterion->getField() !== 'identifier') {
            throw new InvalidArgumentException(sprintf(
                'Only %s field is supported.',
                'identifier',
            ));
        }

        if ($criterion->getOperator() !== FieldValueCriterion::COMPARISON_EQ) {
            throw new InvalidArgumentException(sprintf(
                'Only "%s" comparison operator is supported.',
                FieldValueCriterion::COMPARISON_EQ,
            ));
        }

        if (!is_string($criterion->getValue())) {
            throw new InvalidArgumentException('Only string value for identifier field is supported.');
        }

        return $this->attributeGroupService->getAttributeGroup($criterion->getValue());
    }
}
