<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository;

use Ibexa\Contracts\ProductCatalog\Local\LocalAttributeDefinitionServiceDecorator;
use Ibexa\Contracts\ProductCatalog\Local\LocalAttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeDefinition\AttributeDefinitionCreateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeDefinition\AttributeDefinitionUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\AttributeDefinitionListInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\AttributeDefinitionQuery;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use PHPUnit\Framework\TestCase;

final class LocalAttributeDefinitionServiceDecoratorTest extends TestCase
{
    private const EXAMPLE_IDENTIFIER = 'foo';

    /** @var \Ibexa\Contracts\ProductCatalog\Local\LocalAttributeDefinitionServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private LocalAttributeDefinitionServiceInterface $service;

    private LocalAttributeDefinitionServiceDecorator $decorator;

    protected function setUp(): void
    {
        $this->service = $this->createMock(LocalAttributeDefinitionServiceInterface::class);
        $this->decorator = $this->createDecorator($this->service);
    }

    public function testGetAttributeDefinition(): void
    {
        $expectedAttributeDefinition = $this->createMock(AttributeDefinitionInterface::class);

        $this->service
            ->expects(self::once())
            ->method('getAttributeDefinition')
            ->with(self::EXAMPLE_IDENTIFIER)
            ->willReturn($expectedAttributeDefinition);

        $actualAttributeDefinition = $this->decorator->getAttributeDefinition(self::EXAMPLE_IDENTIFIER);

        self::assertSame($expectedAttributeDefinition, $actualAttributeDefinition);
    }

    public function testFindAttributesDefinitions(): void
    {
        $expectedAttributesDefinitionsList = $this->createMock(AttributeDefinitionListInterface::class);
        $query = new AttributeDefinitionQuery();

        $this->service
            ->expects(self::once())
            ->method('findAttributesDefinitions')
            ->with($query)
            ->willReturn($expectedAttributesDefinitionsList);

        $actualAttributeDefinitionList = $this->decorator->findAttributesDefinitions($query);

        self::assertSame($expectedAttributesDefinitionsList, $actualAttributeDefinitionList);
    }

    public function testCreateAttributeDefinition(): void
    {
        $createStruct = new AttributeDefinitionCreateStruct(self::EXAMPLE_IDENTIFIER);
        $expectedAttributeDefinition = $this->createMock(AttributeDefinitionInterface::class);

        $this->service
            ->expects(self::once())
            ->method('createAttributeDefinition')
            ->with($createStruct)
            ->willReturn($expectedAttributeDefinition);

        $actualAttributeDefinition = $this->decorator->createAttributeDefinition($createStruct);

        self::assertSame($expectedAttributeDefinition, $actualAttributeDefinition);
    }

    public function testDeleteAttributeDefinition(): void
    {
        $attributeDefinition = $this->createMock(AttributeDefinitionInterface::class);

        $this->service
            ->expects(self::once())
            ->method('deleteAttributeDefinition')
            ->with($attributeDefinition);

        $this->decorator->deleteAttributeDefinition($attributeDefinition);
    }

    public function testNewAttributeDefinitionCreateStruct(): void
    {
        $expectedCreateStruct = new AttributeDefinitionCreateStruct(self::EXAMPLE_IDENTIFIER);

        $this->service
            ->expects(self::once())
            ->method('newAttributeDefinitionCreateStruct')
            ->with(self::EXAMPLE_IDENTIFIER)
            ->willReturn($expectedCreateStruct);

        $actualCreateStruct = $this->decorator->newAttributeDefinitionCreateStruct(self::EXAMPLE_IDENTIFIER);

        self::assertSame($expectedCreateStruct, $actualCreateStruct);
    }

    public function testNewAttributeDefinitionUpdateStruct(): void
    {
        $expectedUpdateStruct = new AttributeDefinitionUpdateStruct();
        $attributeDefinition = $this->createMock(AttributeDefinitionInterface::class);

        $this->service
            ->expects(self::once())
            ->method('newAttributeDefinitionUpdateStruct')
            ->with($attributeDefinition)
            ->willReturn($expectedUpdateStruct);

        $actualUpdateStruct = $this->decorator->newAttributeDefinitionUpdateStruct($attributeDefinition);

        self::assertSame($expectedUpdateStruct, $actualUpdateStruct);
    }

    public function testUpdateAttributeDefinition(): void
    {
        $attributeDefinition = $this->createMock(AttributeDefinitionInterface::class);
        $expectedAttributeDefinition = $this->createMock(AttributeDefinitionInterface::class);
        $updateStruct = new AttributeDefinitionUpdateStruct();

        $this->service
            ->expects(self::once())
            ->method('updateAttributeDefinition')
            ->with($attributeDefinition, $updateStruct)
            ->willReturn($expectedAttributeDefinition);

        $actualAttributeDefinition = $this->decorator->updateAttributeDefinition($attributeDefinition, $updateStruct);

        self::assertSame($expectedAttributeDefinition, $actualAttributeDefinition);
    }

    private function createDecorator(
        LocalAttributeDefinitionServiceInterface $service
    ): LocalAttributeDefinitionServiceDecorator {
        return new class($service) extends LocalAttributeDefinitionServiceDecorator {
            // Empty decorator implementation
        };
    }
}
