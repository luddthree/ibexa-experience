<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository;

use Ibexa\Contracts\ProductCatalog\Local\LocalAttributeGroupServiceDecorator;
use Ibexa\Contracts\ProductCatalog\Local\LocalAttributeGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeGroup\AttributeGroupCreateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeGroup\AttributeGroupUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroup\AttributeGroupListInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroup\AttributeGroupQuery;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use PHPUnit\Framework\TestCase;

final class LocalAttributeGroupServiceDecoratorTest extends TestCase
{
    private const EXAMPLE_IDENTIFIER = 'foo';

    /** @var \Ibexa\Contracts\ProductCatalog\Local\LocalAttributeGroupServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private LocalAttributeGroupServiceInterface $service;

    private LocalAttributeGroupServiceDecorator $decorator;

    protected function setUp(): void
    {
        $this->service = $this->createMock(LocalAttributeGroupServiceInterface::class);
        $this->decorator = $this->createDecorator($this->service);
    }

    public function testGetAttributeGroup(): void
    {
        $expectedAttributeGroup = $this->createMock(AttributeGroupInterface::class);

        $this->service
            ->expects(self::once())
            ->method('getAttributeGroup')
            ->with(self::EXAMPLE_IDENTIFIER)
            ->willReturn($expectedAttributeGroup);

        $actualAttributeGroup = $this->decorator->getAttributeGroup(self::EXAMPLE_IDENTIFIER);

        self::assertSame($expectedAttributeGroup, $actualAttributeGroup);
    }

    public function testGetAttributeGroups(): void
    {
        $expectedAttributeGroupList = $this->createMock(AttributeGroupListInterface::class);
        $query = new AttributeGroupQuery();

        $this->service
            ->expects(self::once())
            ->method('findAttributeGroups')
            ->with($query)
            ->willReturn($expectedAttributeGroupList);

        $actualAttributeGroupList = $this->decorator->findAttributeGroups($query);

        self::assertSame($expectedAttributeGroupList, $actualAttributeGroupList);
    }

    public function testCreateAttributeGroup(): void
    {
        $expectedAttributeGroup = $this->createMock(AttributeGroupInterface::class);
        $createStruct = new AttributeGroupCreateStruct(self::EXAMPLE_IDENTIFIER);

        $this->service
            ->expects(self::once())
            ->method('createAttributeGroup')
            ->with($createStruct)
            ->willReturn($expectedAttributeGroup);

        $actualAttributeGroup = $this->decorator->createAttributeGroup($createStruct);

        self::assertSame($expectedAttributeGroup, $actualAttributeGroup);
    }

    public function testDeleteAttributeGroup(): void
    {
        $attributeGroup = $this->createMock(AttributeGroupInterface::class);

        $this->service
            ->expects(self::once())
            ->method('deleteAttributeGroup')
            ->with($attributeGroup);

        $this->decorator->deleteAttributeGroup($attributeGroup);
    }

    public function testNewAttributeGroupCreateStruct(): void
    {
        $expectedCreateStruct = new AttributeGroupCreateStruct(self::EXAMPLE_IDENTIFIER);

        $this->service
            ->expects(self::once())
            ->method('newAttributeGroupCreateStruct')
            ->with(self::EXAMPLE_IDENTIFIER)
            ->willReturn($expectedCreateStruct);

        $actualCreateStruct = $this->decorator->newAttributeGroupCreateStruct(self::EXAMPLE_IDENTIFIER);

        self::assertSame($expectedCreateStruct, $actualCreateStruct);
    }

    public function testNewAttributeGroupUpdateStruct(): void
    {
        $attributeGroup = $this->createMock(AttributeGroupInterface::class);
        $expectedUpdateStruct = new AttributeGroupUpdateStruct();

        $this->service
            ->expects(self::once())
            ->method('newAttributeGroupUpdateStruct')
            ->with($attributeGroup)
            ->willReturn($expectedUpdateStruct);

        $actualUpdateStruct = $this->decorator->newAttributeGroupUpdateStruct($attributeGroup);

        self::assertSame($expectedUpdateStruct, $actualUpdateStruct);
    }

    public function testUpdateAttributeGroup(): void
    {
        $attributeGroup = $this->createMock(AttributeGroupInterface::class);
        $expectedAttributeGroup = $this->createMock(AttributeGroupInterface::class);
        $updateStruct = new AttributeGroupUpdateStruct();

        $this->service
            ->expects(self::once())
            ->method('updateAttributeGroup')
            ->with($attributeGroup, $updateStruct)
            ->willReturn($expectedAttributeGroup);

        $actualAttributeGroup = $this->decorator->updateAttributeGroup($attributeGroup, $updateStruct);

        self::assertSame($expectedAttributeGroup, $actualAttributeGroup);
    }

    private function createDecorator(LocalAttributeGroupServiceInterface $service): LocalAttributeGroupServiceDecorator
    {
        return new class($service) extends LocalAttributeGroupServiceDecorator {
            // Empty decorator implementation
        };
    }
}
