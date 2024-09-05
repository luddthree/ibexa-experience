<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository;

use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeDefinition\AttributeDefinitionUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeGroup;
use Ibexa\Tests\Integration\ProductCatalog\IbexaKernelTestCase;

abstract class BaseAttributeDefinitionServiceTest extends IbexaKernelTestCase
{
    protected const ATTRIBUTE_DEFINITION_POSITION = 1;

    protected function setUp(): void
    {
        self::bootKernel();
        self::getLanguageResolver()->setContextLanguage('eng-US');
        self::setAdministratorUser();
    }

    protected function getUpdateStruct(
        AttributeDefinitionInterface $attributeDefinition
    ): AttributeDefinitionUpdateStruct {
        $attributeDefinitionService = self::getLocalAttributeDefinitionService();

        $updateStruct = $attributeDefinitionService->newAttributeDefinitionUpdateStruct($attributeDefinition);
        $updateStruct->setIdentifier('updated');
        $updateStruct->setName('eng-US', 'Updated');
        $updateStruct->setDescription('eng-US', 'Updated description');
        $updateStruct->setGroup(new AttributeGroup(4, 'fabric', 'Fabric', 0, [], []));
        $updateStruct->setPosition(self::ATTRIBUTE_DEFINITION_POSITION);
        $updateStruct->setOptions(['min' => -10, 'max' => 0]);

        return $updateStruct;
    }

    protected function getAttributeGroup(): AttributeGroup
    {
        return new AttributeGroup(1, 'dimensions', 'Dimensions', 0, [], []);
    }
}
