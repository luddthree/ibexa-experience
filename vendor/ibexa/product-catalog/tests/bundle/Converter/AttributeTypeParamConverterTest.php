<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Converter;

use Ibexa\Bundle\ProductCatalog\Converter\AttributeTypeParamConverter;
use Ibexa\Contracts\ProductCatalog\AttributeTypeServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeTypeInterface;
use Ibexa\Tests\Bundle\Core\Converter\AbstractParamConverterTest;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers \Ibexa\Bundle\ProductCatalog\Converter\AttributeTypeParamConverter
 */
final class AttributeTypeParamConverterTest extends AbstractParamConverterTest
{
    private const PROPERTY_NAME = 'attributeType';

    private const ATTRIBUTE_TYPE_CLASS = AttributeTypeInterface::class;

    private AttributeTypeParamConverter $converter;

    /**
     * @var \Ibexa\Contracts\ProductCatalog\AttributeTypeServiceInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private AttributeTypeServiceInterface $attributeTypeService;

    protected function setUp(): void
    {
        $this->attributeTypeService = $this->createMock(AttributeTypeServiceInterface::class);
        $this->converter = new AttributeTypeParamConverter($this->attributeTypeService);
    }

    public function testSupports(): void
    {
        $config = $this->createConfiguration(self::ATTRIBUTE_TYPE_CLASS);
        self::assertTrue($this->converter->supports($config));

        $config = $this->createConfiguration(__CLASS__);
        self::assertFalse($this->converter->supports($config));

        $config = $this->createConfiguration();
        self::assertFalse($this->converter->supports($config));
    }

    public function testApplyAttributeType(): void
    {
        $identifier = 'foo';
        $valueObject = $this->createMock(AttributeTypeInterface::class);

        $this->attributeTypeService
            ->expects(self::once())
            ->method('getAttributeType')
            ->with($identifier)
            ->willReturn($valueObject);

        $request = new Request([], [], [self::PROPERTY_NAME => $identifier]);
        $config = $this->createConfiguration(self::ATTRIBUTE_TYPE_CLASS, 'attributeType');

        $this->converter->apply($request, $config);

        self::assertInstanceOf(self::ATTRIBUTE_TYPE_CLASS, $request->attributes->get('attributeType'));
    }

    public function testApplyContentOptionalWithEmptyAttribute(): void
    {
        $request = new Request([], [], [self::PROPERTY_NAME => null]);
        $config = $this->createConfiguration(self::ATTRIBUTE_TYPE_CLASS, 'attributeType');

        $config->expects(self::once())
            ->method('isOptional')
            ->willReturn(true);

        self::assertFalse($this->converter->apply($request, $config));
        self::assertNull($request->attributes->get(self::PROPERTY_NAME));
    }
}
