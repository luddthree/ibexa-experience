<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Measurement\ProductCatalog\Local\Attribute;

use Ibexa\Contracts\Core\Test\IbexaKernelTestCase;
use Ibexa\Contracts\Measurement\MeasurementServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\LocalAttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\LocalAttributeGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\LocalProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\LocalProductTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeDefinition\AttributeDefinitionCreateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\ProductType\AssignAttributeDefinitionStruct;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeTypeInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;

abstract class AbstractValueMeasurementTest extends IbexaKernelTestCase
{
    protected MeasurementServiceInterface $measurementService;

    protected LocalProductServiceInterface $productService;

    protected function setUp(): void
    {
        parent::bootKernel();
        parent::setAdministratorUser();

        $this->measurementService = self::getServiceByClassName(MeasurementServiceInterface::class);
        $this->productService = self::getServiceByClassName(LocalProductServiceInterface::class);
    }

    protected function createAttributeGroup(string $identifier): AttributeGroupInterface
    {
        $attributeGroupService = self::getServiceByClassName(LocalAttributeGroupServiceInterface::class);
        $struct = $attributeGroupService->newAttributeGroupCreateStruct($identifier);
        $struct->setNames([
            'eng-GB' => 'FOO',
        ]);

        return $attributeGroupService->createAttributeGroup($struct);
    }

    /**
     * @param array<string,mixed> $options
     */
    protected function createAttributeDefinition(
        AttributeTypeInterface $attributeType,
        AttributeGroupInterface $attributeGroup,
        string $name,
        array $options = []
    ): AttributeDefinitionInterface {
        $attributeDefinitionService = self::getServiceByClassName(LocalAttributeDefinitionServiceInterface::class);
        $createAttributeDefinitionStruct = new AttributeDefinitionCreateStruct($name);
        $createAttributeDefinitionStruct->setType($attributeType);
        $createAttributeDefinitionStruct->setGroup($attributeGroup);
        $createAttributeDefinitionStruct->setOptions(array_merge([
            'unit' => 'centimeter',
            'type' => 'length',
        ], $options));

        return $attributeDefinitionService->createAttributeDefinition($createAttributeDefinitionStruct);
    }

    /**
     * @param array<\Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface> $attributeDefinitions
     */
    protected function createProductType(
        array $attributeDefinitions,
        string $identifier
    ): ProductTypeInterface {
        $productTypeService = self::getServiceByClassName(LocalProductTypeServiceInterface::class);
        $createStruct = $productTypeService->newProductTypeCreateStruct($identifier, 'eng-GB');

        $attributeDefinitionStructs = array_map(static function (AttributeDefinitionInterface $attributeDefinition): AssignAttributeDefinitionStruct {
            return new AssignAttributeDefinitionStruct($attributeDefinition);
        }, $attributeDefinitions);

        $createStruct->setAssignedAttributesDefinitions($attributeDefinitionStructs);

        return $productTypeService->createProductType($createStruct);
    }
}
