<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Migrations\AttributeGroup;

use ArrayIterator;
use Ibexa\Contracts\ProductCatalog\AttributeGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroup\AttributeGroupListInterface;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\Generator\StepBuilder\StepFactoryInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeGroup;
use Ibexa\ProductCatalog\Migrations\AttributeGroup\AttributeGroupMigrationGenerator;
use PHPUnit\Framework\TestCase;
use Traversable;

/**
 * @covers \Ibexa\ProductCatalog\Migrations\AttributeGroup\AttributeGroupMigrationGenerator
 */
final class AttributeGroupMigrationGeneratorTest extends TestCase
{
    private AttributeGroupMigrationGenerator $attributeGroupMigrationGenerator;

    /** @var \Ibexa\Migration\Generator\StepBuilder\StepFactoryInterface&\PHPUnit\Framework\MockObject\MockObject */
    private $stepFactory;

    /** @var \Ibexa\Contracts\ProductCatalog\AttributeGroupServiceInterface&\PHPUnit\Framework\MockObject\MockObject */
    private $attributeGroupService;

    protected function setUp(): void
    {
        $this->attributeGroupService = $this->createMock(AttributeGroupServiceInterface::class);
        $this->stepFactory = $this->createMock(StepFactoryInterface::class);

        $this->attributeGroupMigrationGenerator = new AttributeGroupMigrationGenerator(
            $this->stepFactory,
            $this->attributeGroupService,
        );
    }

    public function testPassesObjectsToStepFactory(): void
    {
        $attributeGroup = $this->createMockAttributeGroup();
        $attributeGroupList = $this->createMock(AttributeGroupListInterface::class);
        $attributeGroupList
            ->expects(self::once())
            ->method('getIterator')
            ->willReturn(new ArrayIterator([$attributeGroup]));

        $this->attributeGroupService
            ->expects(self::once())
            ->method('findAttributeGroups')
            ->willReturn($attributeGroupList);

        $this->stepFactory
            ->expects(self::once())
            ->method('create')
            ->with(self::identicalTo($attributeGroup));

        $iterator = $this->attributeGroupMigrationGenerator->generate(new Mode('create'), [
            'match-property' => null,
            'value' => ['foo'],
        ]);

        // Unpack generator to force execution
        if ($iterator instanceof Traversable) {
            iterator_to_array($iterator);
        }
    }

    private function createMockAttributeGroup(): AttributeGroup
    {
        return new AttributeGroup(
            1,
            'foo',
            'foo_name',
            0,
            [],
            [],
        );
    }
}
