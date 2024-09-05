<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Segmentation\Generator\Segment;

use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\Generator\StepBuilder\StepFactoryInterface;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Segmentation\Generator\Segment\SegmentMigrationGenerator;
use Ibexa\Segmentation\Service\SegmentationService;
use Ibexa\Segmentation\Value\Segment;
use Ibexa\Segmentation\Value\SegmentGroup;
use PHPUnit\Framework\TestCase;
use Traversable;

/**
 * @covers \Ibexa\Segmentation\Generator\Segment\SegmentMigrationGenerator
 */
final class SegmentMigrationGeneratorTest extends TestCase
{
    public function testSupports(): void
    {
        $stepFactory = $this->createMock(StepFactoryInterface::class);
        $segmentationService = $this->createMock(SegmentationService::class);

        $generator = new SegmentMigrationGenerator($stepFactory, $segmentationService);

        $stepFactory->expects($this->once())->method('getSupportedModes')->willReturn(['create']);

        self::assertTrue(
            $generator->supports(
                SegmentMigrationGenerator::TYPE_SEGMENT,
                new Mode('create')
            )
        );
    }

    public function testGenerateAllSegments(): void
    {
        $segmentGroup = new SegmentGroup();
        $segment = new Segment();
        $mode = new Mode('create');

        $stepMock = $this->createMock(StepInterface::class);
        $stepFactory = $this->createMock(StepFactoryInterface::class);
        $segmentationService = $this->createMock(SegmentationService::class);

        $segmentationService->expects(self::atLeastOnce())
            ->method('loadSegmentGroups')
            ->willReturn([$segmentGroup]);

        $segmentationService->expects(self::atLeastOnce())
            ->method('loadSegmentsAssignedToGroup')
            ->willReturn([$segment]);

        $stepFactory->expects(self::atLeastOnce())
            ->method('create')
            ->with($segment, $mode)
            ->willReturn($stepMock);

        $generator = new SegmentMigrationGenerator($stepFactory, $segmentationService);

        $values = $generator->generate($mode, ['value' => []]);
        self::assertInstanceOf(Traversable::class, $values);
        self::assertContainsOnlyInstancesOf(StepInterface::class, $values);
    }
}
