<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\AttributeGroup;

use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use Ibexa\Migration\ValueObject\Step\StepInterface;

final class AttributeGroupUpdateStepExecutor extends AbstractAttributeGroupChangeStepExecutor
{
    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    protected function doHandle(StepInterface $step): AttributeGroupInterface
    {
        assert($step instanceof AttributeGroupUpdateStep);

        $attributeGroup = $this->findAttributeGroup($step);

        $struct = $this->attributeGroupService->newAttributeGroupUpdateStruct($attributeGroup);
        $struct->setIdentifier($step->getIdentifier());
        $struct->setNames($step->getNames());
        $struct->setPosition($step->getPosition());

        return $this->attributeGroupService->updateAttributeGroup($attributeGroup, $struct);
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof AttributeGroupUpdateStep;
    }
}
