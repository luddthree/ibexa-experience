<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\Attribute;

use Ibexa\Contracts\Migration\StepExecutor\AbstractStepExecutor;
use Ibexa\Contracts\ProductCatalog\AttributeGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\LocalAttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\AttributeDefinitionQuery;
use Ibexa\Migration\ValueObject\Step\StepInterface;

final class AttributeUpdateStepExecutor extends AbstractStepExecutor
{
    private LocalAttributeDefinitionServiceInterface $attributeDefinitionService;

    private AttributeGroupServiceInterface $attributeGroupService;

    public function __construct(
        LocalAttributeDefinitionServiceInterface $attributeDefinitionService,
        AttributeGroupServiceInterface $attributeGroupService
    ) {
        $this->attributeDefinitionService = $attributeDefinitionService;
        $this->attributeGroupService = $attributeGroupService;
    }

    /**
     * @return \Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface[]
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    protected function doHandle(StepInterface $step): array
    {
        assert($step instanceof AttributeUpdateStep);

        $query = new AttributeDefinitionQuery($step->getCriterion());
        $query->setLimit(null);
        $attributeDefinitions = $this->attributeDefinitionService->findAttributesDefinitions($query);

        $changedAttributeDefinitions = [];
        foreach ($attributeDefinitions->getAttributeDefinitions() as $attributeDefinition) {
            $struct = $this->attributeDefinitionService->newAttributeDefinitionUpdateStruct($attributeDefinition);

            if ($step->getIdentifier() !== null) {
                $struct->setIdentifier($step->getIdentifier());
            }

            if ($step->getAttributeGroupIdentifier() !== null) {
                $attributeGroup = $this->attributeGroupService->getAttributeGroup(
                    $step->getAttributeGroupIdentifier()
                );
                $struct->setGroup($attributeGroup);
            } else {
                $struct->setGroup($attributeDefinition->getGroup());
            }

            if ($step->getPosition() !== null) {
                $struct->setPosition($step->getPosition());
            }

            foreach ($step->getNames() as $languageCode => $name) {
                $struct->setName($languageCode, $name);
            }

            foreach ($step->getDescriptions() as $languageCode => $description) {
                $struct->setDescription($languageCode, $description ?? '');
            }

            if ($step->getOptions() !== null) {
                $struct->setOptions($step->getOptions());
            }

            $changedAttributeDefinitions[] = $this->attributeDefinitionService->updateAttributeDefinition(
                $attributeDefinition,
                $struct,
            );
        }

        return $changedAttributeDefinitions;
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof AttributeUpdateStep;
    }
}
