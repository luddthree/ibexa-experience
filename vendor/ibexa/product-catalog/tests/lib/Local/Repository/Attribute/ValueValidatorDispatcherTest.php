<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Repository\Attribute;

use Ibexa\Contracts\ProductCatalog\Local\Attribute\ValueValidationError;
use Ibexa\Contracts\ProductCatalog\Local\Attribute\ValueValidatorInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeTypeInterface;
use Ibexa\ProductCatalog\Local\Repository\Attribute\ValueValidatorDispatcher;
use Ibexa\ProductCatalog\Local\Repository\Attribute\ValueValidatorRegistryInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinitionOptions;
use PHPUnit\Framework\TestCase;

final class ValueValidatorDispatcherTest extends TestCase
{
    private const EXAMPLE_TYPE_IDENTIFIER = 'integer';
    private const EXAMPLE_OPTIONS = [
        'min' => 0,
        'max' => 100,
    ];
    private const EXAMPLE_VALUE = 7;

    /** @var \Ibexa\ProductCatalog\Local\Repository\Attribute\ValueValidatorRegistryInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ValueValidatorRegistryInterface $registry;

    private ValueValidatorDispatcher $dispatcher;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ValueValidatorRegistryInterface::class);
        $this->dispatcher = new ValueValidatorDispatcher($this->registry);
    }

    public function testDispatch(): void
    {
        $definition = $this->createAttributeDefinition(
            self::EXAMPLE_TYPE_IDENTIFIER,
            self::EXAMPLE_OPTIONS
        );

        $expectedErrors = [
            new ValueValidationError('value', 'Example validation error'),
        ];

        $validator = $this->createMock(ValueValidatorInterface::class);
        $validator
            ->expects(self::once())
            ->method('validateValue')
            ->with($definition, self::EXAMPLE_VALUE)
            ->willReturn($expectedErrors);

        $this->registry
            ->method('hasValidator')
            ->with(self::EXAMPLE_TYPE_IDENTIFIER)
            ->willReturn(true);

        $this->registry
            ->method('getValidator')
            ->with(self::EXAMPLE_TYPE_IDENTIFIER)
            ->willReturn($validator);

        $actualErrors = $this->dispatcher->validateValue($definition, self::EXAMPLE_VALUE);

        self::assertEquals($expectedErrors, $actualErrors);
    }

    public function testDispatchForMissingValidator(): void
    {
        $definition = $this->createAttributeDefinition(
            self::EXAMPLE_TYPE_IDENTIFIER,
            self::EXAMPLE_OPTIONS
        );

        $this->registry
            ->method('hasValidator')
            ->with(self::EXAMPLE_TYPE_IDENTIFIER)
            ->willReturn(false);

        $this->registry
            ->expects(self::never())
            ->method('getValidator')
            ->with(self::EXAMPLE_TYPE_IDENTIFIER);

        self::assertEmpty($this->dispatcher->validateValue($definition, self::EXAMPLE_VALUE));
    }

    /**
     * @param array<string,mixed> $options
     */
    private function createAttributeDefinition(string $typeIdentifier, array $options): AttributeDefinitionInterface
    {
        $type = $this->createMock(AttributeTypeInterface::class);
        $type->method('getIdentifier')->willReturn($typeIdentifier);

        $definition = $this->createMock(AttributeDefinitionInterface::class);
        $definition->method('getType')->willReturn($type);
        $definition->method('getOptions')->willReturn(new AttributeDefinitionOptions($options));

        return $definition;
    }
}
