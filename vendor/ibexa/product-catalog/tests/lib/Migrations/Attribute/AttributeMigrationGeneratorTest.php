<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Migrations\Attribute;

use ArrayIterator;
use Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\AttributeDefinitionListInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeTypeInterface;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\Generator\StepBuilder\StepFactoryInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\AttributeDefinition;
use Ibexa\ProductCatalog\Migrations\Attribute\AttributeMigrationGenerator;
use PHPUnit\Framework\TestCase;
use Traversable;

/**
 * @covers \Ibexa\ProductCatalog\Migrations\Attribute\AttributeMigrationGenerator
 */
final class AttributeMigrationGeneratorTest extends TestCase
{
    private AttributeMigrationGenerator $attributeMigrationGenerator;

    /** @var \Ibexa\Migration\Generator\StepBuilder\StepFactoryInterface&\PHPUnit\Framework\MockObject\MockObject */
    private $stepFactory;

    /** @var \Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface&\PHPUnit\Framework\MockObject\MockObject */
    private $attributeDefinitionService;

    protected function setUp(): void
    {
        $this->attributeDefinitionService = $this->createMock(AttributeDefinitionServiceInterface::class);
        $this->stepFactory = $this->createMock(StepFactoryInterface::class);

        $this->attributeMigrationGenerator = new AttributeMigrationGenerator(
            $this->stepFactory,
            $this->attributeDefinitionService,
        );
    }

    public function testPassesObjectsToStepFactory(): void
    {
        $attributeDefinition = $this->createMockAttributeDefinition();
        $attributeDefinitionList = $this->createMock(AttributeDefinitionListInterface::class);
        $attributeDefinitionList
            ->expects(self::once())
            ->method('getIterator')
            ->willReturn(new ArrayIterator([$attributeDefinition]));

        $this->attributeDefinitionService
            ->expects(self::once())
            ->method('findAttributesDefinitions')
            ->willReturn($attributeDefinitionList);

        $this->stepFactory
            ->expects(self::once())
            ->method('create')
            ->with(self::identicalTo($attributeDefinition));

        $iterator = $this->attributeMigrationGenerator->generate(new Mode('create'), [
            'match-property' => null,
            'value' => ['foo'],
        ]);

        // Unpack generator to force execution
        if ($iterator instanceof Traversable) {
            iterator_to_array($iterator);
        }
    }

    private function createMockAttributeDefinition(): AttributeDefinition
    {
        return new AttributeDefinition(
            1,
            'foo',
            $this->createMock(AttributeTypeInterface::class),
            $this->createMock(AttributeGroupInterface::class),
            'foo_name',
            0,
            [],
            null,
            [],
            [],
        );
    }
}
