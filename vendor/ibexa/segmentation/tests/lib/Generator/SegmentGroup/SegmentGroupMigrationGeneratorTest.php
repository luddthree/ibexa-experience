<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Segmentation\Generator\SegmentGroup;

use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\Generator\StepBuilder\StepFactoryInterface;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Segmentation\Generator\SegmentGroup\SegmentGroupMigrationGenerator;
use Ibexa\Segmentation\Service\SegmentationService;
use Ibexa\Segmentation\Value\SegmentGroup;
use PHPUnit\Framework\TestCase;
use Traversable;

/**
 * @covers \Ibexa\Segmentation\Generator\SegmentGroup\SegmentGroupMigrationGenerator
 */
final class SegmentGroupMigrationGeneratorTest extends TestCase
{
    public function testSupports(): void
    {
        $stepFactory = $this->createMock(StepFactoryInterface::class);
        $segmentationService = $this->createMock(SegmentationService::class);

        $generator = new SegmentGroupMigrationGenerator($stepFactory, $segmentationService);

        $stepFactory->expects($this->once())->method('getSupportedModes')->willReturn(['create']);

        self::assertTrue(
            $generator->supports(
                SegmentGroupMigrationGenerator::TYPE_SEGMENT_GROUP,
                new Mode('create')
            )
        );
    }

    public function testGenerateAllSegmentGroups(): void
    {
        $segmentGroup = new SegmentGroup();
        $mode = new Mode('create');

        $stepMock = $this->createMock(StepInterface::class);
        $stepFactory = $this->createMock(StepFactoryInterface::class);
        $segmentationService = $this->createMock(SegmentationService::class);

        $segmentationService->expects(self::atLeastOnce())
            ->method('loadSegmentGroups')
            ->willReturn([$segmentGroup]);

        $stepFactory->expects(self::atLeastOnce())
            ->method('create')
            ->with($segmentGroup, $mode)
            ->willReturn($stepMock);

        $generator = new SegmentGroupMigrationGenerator($stepFactory, $segmentationService);

        $values = $generator->generate(new Mode('create'), ['value' => []]);
        self::assertInstanceOf(Traversable::class, $values);
        self::assertContainsOnlyInstancesOf(StepInterface::class, $values);
    }
}
