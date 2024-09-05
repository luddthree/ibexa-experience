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
use Ibexa\Migration\ValueObject\Step\StepInterface;

final class AttributeGroupCreateStepExecutor extends AbstractStepExecutor
{
    private LocalAttributeGroupServiceInterface $attributeGroupService;

    public function __construct(LocalAttributeGroupServiceInterface $attributeGroupService)
    {
        $this->attributeGroupService = $attributeGroupService;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    protected function doHandle(StepInterface $step): AttributeGroupInterface
    {
        assert($step instanceof AttributeGroupCreateStep);

        $struct = $this->attributeGroupService->newAttributeGroupCreateStruct($step->getIdentifier());
        $struct->setNames($step->getNames());
        $struct->setPosition($step->getPosition());

        return $this->attributeGroupService->createAttributeGroup($struct);
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof AttributeGroupCreateStep;
    }
}
