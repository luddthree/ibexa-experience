<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Repository\CatalogFilter\DefinitionProvider;

use ArrayIterator;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\CatalogFilters\Attribute\FilterDefinitionFactoryInterface;
use Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterDefinitionInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\AttributeDefinitionListInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeTypeInterface;
use Ibexa\ProductCatalog\Local\Repository\CatalogFilter\DefinitionProvider\AttributeFilterDefinitionProvider;
use Ibexa\ProductCatalog\Local\Repository\CatalogFilter\DefinitionProvider\Exception\UnknownFilterDefinitionException;
use PHPUnit\Framework\TestCase;
use Traversable;

final class AttributeFilterDefinitionProviderTest extends TestCase
{
    public function testHasFilterDefinition(): void
    {
        $attributeDefinitionService = $this->createAttributeDefinitionService(
            'existing',
            $this->createAttributeDefinition('existing', 'integer')
        );

        $provider = new AttributeFilterDefinitionProvider(
            $attributeDefinitionService,
            [
                'integer' => $this->createMock(FilterDefinitionFactoryInterface::class),
            ]
        );

        self::assertTrue($provider->hasFilterDefinition('product_attribute_existing'));
    }

    public function testHasFilterDefinitionWithNotSupportedIdentifier(): void
    {
        $provider = new AttributeFilterDefinitionProvider(
            $this->createMock(AttributeDefinitionServiceInterface::class)
        );

        self::assertFalse($provider->hasFilterDefinition('product_type'));
    }

    public function testHasFilterDefinitionForNonExistingAttributeDefinition(): void
    {
        $provider = $this->createProviderForNonExistingAttributeDefinitionTest('non_existing');

        self::assertFalse($provider->hasFilterDefinition('product_attribute_non_existing'));
    }

    public function testHasFilterDefinitionForNotSupportedAttributeType(): void
    {
        $provider = $this->createProviderForNotSupportedAttributeTypeTest('foo');

        self::assertFalse($provider->hasFilterDefinition('foo'));
    }

    private function createProviderForNonExistingAttributeDefinitionTest(string $identifier): AttributeFilterDefinitionProvider
    {
        $attributeDefinitionService = $this->createMock(AttributeDefinitionServiceInterface::class);
        $attributeDefinitionService
            ->method('getAttributeDefinition')
            ->with($identifier)
            ->willThrowException($this->createMock(NotFoundException::class));

        return new AttributeFilterDefinitionProvider($attributeDefinitionService);
    }

    private function createProviderForNotSupportedAttributeTypeTest(string $identifier): AttributeFilterDefinitionProvider
    {
        $attributeDefinitionService = $this->createAttributeDefinitionService(
            $identifier,
            $this->createAttributeDefinition($identifier, 'not_supported')
        );

        return new AttributeFilterDefinitionProvider(
            $attributeDefinitionService,
            [
                'example' => $this->createMock(FilterDefinitionFactoryInterface::class),
            ]
        );
    }

    public function testGetFilterDefinition(): void
    {
        $expectedFilterDefinition = $this->createMock(FilterDefinitionInterface::class);

        $attributeDefinition = $this->createAttributeDefinition('example', 'integer');

        $filterDefinitionFactory = $this->createFilterDefinitionFactory(
            $attributeDefinition,
            'product_attribute_example',
            $expectedFilterDefinition
        );

        $provider = new AttributeFilterDefinitionProvider(
            $this->createAttributeDefinitionService('example', $attributeDefinition),
            [
                'integer' => $filterDefinitionFactory,
            ]
        );

        self::assertSame($expectedFilterDefinition, $provider->getFilterDefinition('product_attribute_example'));
    }

    public function testGetFilterDefinitionWithNotSupportedIdentifier(): void
    {
        $this->expectException(UnknownFilterDefinitionException::class);

        $provider = new AttributeFilterDefinitionProvider(
            $this->createMock(AttributeDefinitionServiceInterface::class)
        );

        $provider->getFilterDefinition('example');
    }

    public function testGetFilterDefinitionWithNotSupportedAttributeType(): void
    {
        $this->expectException(UnknownFilterDefinitionException::class);

        $provider = $this->createProviderForNotSupportedAttributeTypeTest('example');
        $provider->getFilterDefinition('example');
    }

    public function testGetFilterDefinitions(): void
    {
        $foo = $this->createAttributeDefinition('foo', 'foo');
        $bar = $this->createAttributeDefinition('bar', 'bar');
        $baz = $this->createAttributeDefinition('baz', 'baz');

        $attributeDefinitionService = $this->createMock(AttributeDefinitionServiceInterface::class);
        $attributeDefinitionService
            ->method('findAttributesDefinitions')
            ->willReturn($this->createAttributeDefinitionList([$foo, $bar, $baz]));

        $expectedFooFilterDefinition = $this->createMock(FilterDefinitionInterface::class);
        $expectedBarFilterDefinition = $this->createMock(FilterDefinitionInterface::class);
        $expectedBazFilterDefinition = $this->createMock(FilterDefinitionInterface::class);

        $provider = new AttributeFilterDefinitionProvider(
            $attributeDefinitionService,
            [
                'foo' => $this->createFilterDefinitionFactory($foo, 'product_attribute_foo', $expectedFooFilterDefinition),
                'bar' => $this->createFilterDefinitionFactory($bar, 'product_attribute_bar', $expectedBarFilterDefinition),
                'baz' => $this->createFilterDefinitionFactory($baz, 'product_attribute_baz', $expectedBazFilterDefinition),
            ]
        );

        $actualResult = $provider->getFilterDefinitions();
        if ($actualResult instanceof Traversable) {
            $actualResult = iterator_to_array($actualResult);
        }

        self::assertEquals([
            $expectedFooFilterDefinition,
            $expectedBarFilterDefinition,
            $expectedBazFilterDefinition,
        ], $actualResult);
    }

    public function testGetDefaultFilterIdentifiers(): void
    {
        $provider = new AttributeFilterDefinitionProvider(
            $this->createMock(AttributeDefinitionServiceInterface::class)
        );

        self::assertSame([], $provider->getDefaultFilterIdentifiers());
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface[] $definitions
     */
    private function createAttributeDefinitionList(array $definitions): AttributeDefinitionListInterface
    {
        $list = $this->createMock(AttributeDefinitionListInterface::class);
        $list->method('getTotalCount')->willReturn(count($definitions));
        $list->method('getAttributeDefinitions')->willReturn($definitions);
        $list->method('getIterator')->willReturn(new ArrayIterator($definitions));

        return $list;
    }

    private function createAttributeDefinitionService(
        string $identifier,
        AttributeDefinitionInterface $definition
    ): AttributeDefinitionServiceInterface {
        $service = $this->createMock(AttributeDefinitionServiceInterface::class);
        $service->method('getAttributeDefinition')->with($identifier)->willReturn($definition);

        return $service;
    }

    private function createFilterDefinitionFactory(
        AttributeDefinitionInterface $attributeDefinition,
        string $identifier,
        FilterDefinitionInterface $expectedDefinition
    ): FilterDefinitionFactoryInterface {
        $factory = $this->createMock(FilterDefinitionFactoryInterface::class);
        $factory
            ->method('createFilterDefinition')
            ->with($attributeDefinition, $identifier)
            ->willReturn($expectedDefinition);

        return $factory;
    }

    private function createAttributeDefinition(
        string $identifier,
        string $typeIdentifier
    ): AttributeDefinitionInterface {
        $attributeType = $this->createMock(AttributeTypeInterface::class);
        $attributeType->method('getIdentifier')->willReturn($typeIdentifier);

        $attributeDefinition = $this->createMock(AttributeDefinitionInterface::class);
        $attributeDefinition->method('getIdentifier')->willReturn($identifier);
        $attributeDefinition->method('getType')->willReturn($attributeType);

        return $attributeDefinition;
    }
}
