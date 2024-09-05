<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Segmentation\StepExecutor;

use Ibexa\Contracts\Segmentation\SegmentationServiceInterface;
use Ibexa\Migration\Tests\StepExecutor\AbstractStepExecutorTest;
use Ibexa\Segmentation\StepExecutor\SegmentDeleteStepExecutor;
use Ibexa\Segmentation\Value\SegmentCreateStruct;
use Ibexa\Segmentation\Value\SegmentGroupCreateStruct;
use Ibexa\Segmentation\Value\SegmentMatcher;
use Ibexa\Segmentation\Value\Step\SegmentDeleteStep;

/**
 * @covers \Ibexa\Segmentation\StepExecutor\SegmentDeleteStepExecutor
 */
final class SegmentDeleteStepExecutorTest extends AbstractStepExecutorTest
{
    private SegmentationServiceInterface $segmentationService;

    private SegmentDeleteStepExecutor $executor;

    public function setUp(): void
    {
        self::bootKernel();

        $this->segmentationService = self::getServiceByClassName(SegmentationServiceInterface::class);
        $this->executor = new SegmentDeleteStepExecutor(
            $this->segmentationService,
        );
        self::configureExecutor($this->executor);

        self::setAdministratorUser();
    }

    public function testDeletingSegment(): void
    {
        $segmentGroup = $this->segmentationService->createSegmentGroup(new SegmentGroupCreateStruct([
            'identifier' => 'segment_group_foo',
            'name' => 'SEGMENT GROUP foo NAME',
            'createSegments' => [
                new SegmentCreateStruct([
                    'identifier' => 'foo identifier',
                    'name' => 'foo name',
                ]),
                new SegmentCreateStruct([
                    'identifier' => 'bar identifier',
                    'name' => 'bar name',
                ]),
            ],
        ]));

        $segments = $this->segmentationService->loadSegmentsAssignedToGroup($segmentGroup);

        self::assertCount(2, $segments);

        $step = new SegmentDeleteStep(
            new SegmentMatcher(null, 'foo identifier'),
        );

        $this->executor->handle($step);

        $segments = $this->segmentationService->loadSegmentsAssignedToGroup($segmentGroup);

        self::assertCount(1, $segments);
        $segment = $segments[0];
        self::assertSame('bar identifier', $segment->identifier);
        self::assertSame('bar name', $segment->name);
        self::assertSame('segment_group_foo', $segment->group->identifier);
    }
}
