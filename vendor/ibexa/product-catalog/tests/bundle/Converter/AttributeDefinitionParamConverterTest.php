<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Converter;

use Ibexa\Bundle\ProductCatalog\Converter\AttributeDefinitionParamConverter;
use Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Ibexa\Tests\Bundle\Core\Converter\AbstractParamConverterTest;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers \Ibexa\Bundle\ProductCatalog\Converter\AttributeDefinitionParamConverter
 */
final class AttributeDefinitionParamConverterTest extends AbstractParamConverterTest
{
    private const PROPERTY_NAME = 'attributeDefinitionIdentifier';

    private const ATTRIBUTE_DEFINITION_CLASS = AttributeDefinitionInterface::class;

    private AttributeDefinitionParamConverter $converter;

    /**
     * @var \Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private AttributeDefinitionServiceInterface $attributeDefinitionService;

    protected function setUp(): void
    {
        $this->attributeDefinitionService = $this->createMock(AttributeDefinitionServiceInterface::class);
        $this->converter = new AttributeDefinitionParamConverter($this->attributeDefinitionService);
    }

    public function testSupports(): void
    {
        $config = $this->createConfiguration(self::ATTRIBUTE_DEFINITION_CLASS);
        self::assertTrue($this->converter->supports($config));

        $config = $this->createConfiguration(__CLASS__);
        self::assertFalse($this->converter->supports($config));

        $config = $this->createConfiguration();
        self::assertFalse($this->converter->supports($config));
    }

    public function testApplyAttributeDefinition(): void
    {
        $identifier = 'foo';
        $valueObject = $this->createMock(AttributeDefinitionInterface::class);

        $this->attributeDefinitionService
            ->expects(self::once())
            ->method('getAttributeDefinition')
            ->with($identifier)
            ->willReturn($valueObject);

        $request = new Request([], [], [self::PROPERTY_NAME => $identifier]);
        $config = $this->createConfiguration(self::ATTRIBUTE_DEFINITION_CLASS, 'attributeDefinition');

        $this->converter->apply($request, $config);

        self::assertInstanceOf(self::ATTRIBUTE_DEFINITION_CLASS, $request->attributes->get('attributeDefinition'));
    }

    public function testApplyContentOptionalWithEmptyAttribute(): void
    {
        $request = new Request([], [], [self::PROPERTY_NAME => null]);
        $config = $this->createConfiguration(self::ATTRIBUTE_DEFINITION_CLASS, 'attributeDefinition');

        $config->expects(self::once())
            ->method('isOptional')
            ->willReturn(true);

        self::assertFalse($this->converter->apply($request, $config));
        self::assertNull($request->attributes->get(self::PROPERTY_NAME));
    }
}
