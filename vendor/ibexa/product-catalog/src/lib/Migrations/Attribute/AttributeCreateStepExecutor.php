<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\Attribute;

use Ibexa\Contracts\Migration\StepExecutor\AbstractStepExecutor;
use Ibexa\Contracts\ProductCatalog\AttributeGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\AttributeTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\LocalAttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Ibexa\Migration\ValueObject\Step\StepInterface;

final class AttributeCreateStepExecutor extends AbstractStepExecutor
{
    private LocalAttributeDefinitionServiceInterface $attributeDefinitionService;

    private AttributeTypeServiceInterface $attributeTypeService;

    private AttributeGroupServiceInterface $attributeGroupService;

    public function __construct(
        LocalAttributeDefinitionServiceInterface $attributeDefinitionService,
        AttributeTypeServiceInterface $attributeTypeService,
        AttributeGroupServiceInterface $attributeGroupService
    ) {
        $this->attributeDefinitionService = $attributeDefinitionService;
        $this->attributeTypeService = $attributeTypeService;
        $this->attributeGroupService = $attributeGroupService;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    protected function doHandle(StepInterface $step): AttributeDefinitionInterface
    {
        assert($step instanceof AttributeCreateStep);

        $struct = $this->attributeDefinitionService->newAttributeDefinitionCreateStruct($step->getIdentifier());

        $attributeType = $this->attributeTypeService->getAttributeType($step->getAttributeTypeIdentifier());
        $struct->setType($attributeType);

        $attributeGroup = $this->attributeGroupService->getAttributeGroup($step->getAttributeGroupIdentifier());
        $struct->setGroup($attributeGroup);

        $struct->setPosition($step->getPosition());

        foreach ($step->getNames() as $languageCode => $name) {
            $struct->setName($languageCode, $name);
        }

        foreach ($step->getDescriptions() as $languageCode => $description) {
            $struct->setDescription($languageCode, $description ?? '');
        }

        $struct->setOptions($step->getOptions());

        return $this->attributeDefinitionService->createAttributeDefinition($struct);
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof AttributeCreateStep;
    }
}
