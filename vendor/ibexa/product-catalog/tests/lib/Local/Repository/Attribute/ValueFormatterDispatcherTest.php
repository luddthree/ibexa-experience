<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Repository\Attribute;

use Ibexa\Contracts\ProductCatalog\Local\Attribute\ValueFormatterInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeTypeInterface;
use Ibexa\ProductCatalog\Local\Repository\Attribute\ValueFormatterDispatcher;
use Ibexa\ProductCatalog\Local\Repository\Attribute\ValueFormatterRegistryInterface;
use PHPUnit\Framework\TestCase;

final class ValueFormatterDispatcherTest extends TestCase
{
    private const EXAMPLE_TYPE_IDENTIFIER = 'float';

    private const EXAMPLE_PARAMETERS = [
        'foo' => 'foo',
        'bar' => 'bar',
        'baz' => 'baz',
    ];

    /** @var \Ibexa\ProductCatalog\Local\Repository\Attribute\ValueFormatterRegistryInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ValueFormatterRegistryInterface $registry;

    private ValueFormatterDispatcher $dispatcher;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ValueFormatterRegistryInterface::class);
        $this->dispatcher = new ValueFormatterDispatcher($this->registry);
    }

    public function testDispatch(): void
    {
        $expectedValue = 'Human readable value with fancy formatting';

        $attribute = $this->createExampleAttribute(self::EXAMPLE_TYPE_IDENTIFIER);

        $formatter = $this->createMock(ValueFormatterInterface::class);
        $formatter
            ->method('formatValue')
            ->with($attribute, self::EXAMPLE_PARAMETERS)
            ->willReturn($expectedValue);

        $this->registry
            ->method('hasFormatter')
            ->with(self::EXAMPLE_TYPE_IDENTIFIER)
            ->willReturn(true);

        $this->registry
            ->method('getFormatter')
            ->with(self::EXAMPLE_TYPE_IDENTIFIER)
            ->willReturn($formatter);

        $actualValue = $this->dispatcher->formatValue($attribute, self::EXAMPLE_PARAMETERS);

        self::assertEquals($expectedValue, $actualValue);
    }

    public function testDispatchForMissingFormatter(): void
    {
        $attribute = $this->createExampleAttribute(self::EXAMPLE_TYPE_IDENTIFIER);

        $this->registry
            ->method('hasFormatter')
            ->with(self::EXAMPLE_TYPE_IDENTIFIER)
            ->willReturn(false);

        $this->registry
            ->expects(self::never())
            ->method('getFormatter')
            ->with(self::EXAMPLE_TYPE_IDENTIFIER);

        self::assertNull($this->dispatcher->formatValue($attribute, self::EXAMPLE_PARAMETERS));
    }

    private function createExampleAttribute(string $typeIdentifier): AttributeInterface
    {
        $type = $this->createMock(AttributeTypeInterface::class);
        $type->method('getIdentifier')->willReturn($typeIdentifier);

        $definition = $this->createMock(AttributeDefinitionInterface::class);
        $definition->method('getType')->willReturn($type);

        $attribute = $this->createMock(AttributeInterface::class);
        $attribute->method('getAttributeDefinition')->willReturn($definition);

        return $attribute;
    }
}
