<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\AttributeGroup;

use Ibexa\Migration\ValueObject\Step\StepInterface;

final class AttributeGroupDeleteStepExecutor extends AbstractAttributeGroupChangeStepExecutor
{
    protected function doHandle(StepInterface $step)
    {
        assert($step instanceof AttributeGroupDeleteStep);

        $attributeGroup = $this->findAttributeGroup($step);
        $this->attributeGroupService->deleteAttributeGroup($attributeGroup);

        return null;
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof AttributeGroupDeleteStep;
    }
}
