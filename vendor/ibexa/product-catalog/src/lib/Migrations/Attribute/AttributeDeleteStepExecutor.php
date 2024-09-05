<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\Attribute;

use Ibexa\Contracts\Migration\StepExecutor\AbstractStepExecutor;
use Ibexa\Contracts\ProductCatalog\Local\LocalAttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\AttributeDefinitionQuery;
use Ibexa\Migration\ValueObject\Step\StepInterface;

final class AttributeDeleteStepExecutor extends AbstractStepExecutor
{
    private LocalAttributeDefinitionServiceInterface $attributeDefinitionService;

    public function __construct(LocalAttributeDefinitionServiceInterface $attributeDefinitionService)
    {
        $this->attributeDefinitionService = $attributeDefinitionService;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    protected function doHandle(StepInterface $step)
    {
        assert($step instanceof AttributeDeleteStep);

        $query = new AttributeDefinitionQuery($step->getCriterion());
        $query->setLimit(null);
        $attributeDefinitions = $this->attributeDefinitionService->findAttributesDefinitions($query);

        foreach ($attributeDefinitions->getAttributeDefinitions() as $attributeDefinition) {
            $this->attributeDefinitionService->deleteAttributeDefinition($attributeDefinition);
        }

        return null;
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof AttributeDeleteStep;
    }
}
