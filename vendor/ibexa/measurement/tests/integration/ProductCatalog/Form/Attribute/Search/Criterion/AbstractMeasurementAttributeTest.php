<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Measurement\ProductCatalog\Form\Attribute\Search\Criterion;

use Ibexa\Contracts\Core\Test\IbexaKernelTestCase;
use Ibexa\Contracts\Measurement\MeasurementServiceInterface;
use Ibexa\Contracts\ProductCatalog\AttributeTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\LocalAttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\LocalAttributeGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\LocalProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\LocalProductTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\Values\ProductType\AssignAttributeDefinitionStruct;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeTypeInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;

/**
 * @phpstan-type CriterionFactory callable(): \Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface
 */
abstract class AbstractMeasurementAttributeTest extends IbexaKernelTestCase
{
    protected const ATTRIBUTE_RANGE_FOO = 'range_attribute';
    protected const ATTRIBUTE_SIMPLE_FOO = 'simple_attribute';

    protected MeasurementServiceInterface $measurementService;

    protected LocalProductServiceInterface $productService;

    private ProductTypeInterface $productType;

    protected function setUp(): void
    {
        parent::bootKernel();
        parent::setAdministratorUser();

        $this->measurementService = self::getServiceByClassName(MeasurementServiceInterface::class);
        $this->productService = self::getServiceByClassName(LocalProductServiceInterface::class);
    }

    /**
     * @param string[] $expectedCodes
     *
     * @phpstan-param CriterionFactory $criterionFactory
     *
     * @dataProvider provideForQuerying
     */
    public function testQuerying(array $expectedCodes, callable $criterionFactory): void
    {
        $criterion = $criterionFactory();
        $query = new ProductQuery();
        $query->setQuery($criterion);
        $products = $this->productService->findProducts($query);
        $codes = array_map(
            static fn (ProductInterface $product): string => $product->getCode(),
            $products->getProducts(),
        );

        $expectedCount = count($expectedCodes);
        self::assertCount(
            $expectedCount,
            $codes,
            sprintf(
                'Found matching products with codes: "%s", expected "%s".',
                implode('", "', $codes),
                implode('", "', $expectedCodes),
            ),
        );

        self::assertEquals($expectedCodes, $codes);
    }

    /**
     * @return iterable<string, array{
     *     string[],
     *     CriterionFactory,
     * }>
     */
    abstract public function provideForQuerying(): iterable;

    /**
     * @param array<string, \Ibexa\Contracts\Measurement\Value\ValueInterface> $attributes
     */
    final protected function addProduct(string $code, array $attributes): void
    {
        $productType = $this->getTestProductType();

        $createStruct = $this->productService->newProductCreateStruct($productType, 'eng-GB');
        $createStruct->setCode($code);
        $createStruct->setField('name', 'foo');

        foreach ($attributes as $attributeName => $value) {
            $createStruct->setAttribute($attributeName, $value);
        }

        $this->productService->createProduct($createStruct);
    }

    final protected function getTestProductType(): ProductTypeInterface
    {
        return $this->productType ??= $this->initializeProductType();
    }

    private function createAttributeGroup(): AttributeGroupInterface
    {
        $attributeGroupService = self::getServiceByClassName(LocalAttributeGroupServiceInterface::class);
        $struct = $attributeGroupService->newAttributeGroupCreateStruct('foo');
        $struct->setNames([
            'eng-GB' => 'FOO',
        ]);

        return $attributeGroupService->createAttributeGroup($struct);
    }

    /**
     * @param array<string, mixed> $options
     */
    private function createAttributeDefinition(
        AttributeTypeInterface $rangeAttributeType,
        AttributeGroupInterface $attributeGroup,
        string $attributeIdentifier,
        array $options = []
    ): AttributeDefinitionInterface {
        $attributeDefinitionService = self::getServiceByClassName(LocalAttributeDefinitionServiceInterface::class);

        $struct = $attributeDefinitionService->newAttributeDefinitionCreateStruct(
            $attributeIdentifier
        );
        $struct->setType($rangeAttributeType);
        $struct->setGroup($attributeGroup);
        $struct->setOptions($options);

        return $attributeDefinitionService->createAttributeDefinition($struct);
    }

    private function initializeProductType(): ProductTypeInterface
    {
        $attributeTypeService = self::getServiceByClassName(AttributeTypeServiceInterface::class);
        $rangeAttributeType = $attributeTypeService->getAttributeType('measurement_range');
        $simpleAttributeType = $attributeTypeService->getAttributeType('measurement_single');

        $attributeGroup = $this->createAttributeGroup();

        $productTypeService = self::getServiceByClassName(LocalProductTypeServiceInterface::class);
        $createStruct = $productTypeService->newProductTypeCreateStruct('foo', 'eng-GB');
        $createStruct->setAssignedAttributesDefinitions([
            new AssignAttributeDefinitionStruct(
                $this->createAttributeDefinition(
                    $rangeAttributeType,
                    $attributeGroup,
                    self::ATTRIBUTE_RANGE_FOO,
                    [
                        'type' => 'length',
                        'unit' => 'centimeter',
                    ],
                ),
                false
            ),
            new AssignAttributeDefinitionStruct(
                $this->createAttributeDefinition(
                    $simpleAttributeType,
                    $attributeGroup,
                    self::ATTRIBUTE_SIMPLE_FOO,
                    [
                        'type' => 'length',
                        'unit' => 'centimeter',
                        'sign' => 'none',
                    ],
                ),
                false
            ),
        ]);

        return $productTypeService->createProductType($createStruct);
    }
}
