<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Segmentation\StepExecutor;

use Ibexa\Contracts\Segmentation\SegmentationServiceInterface;
use Ibexa\Migration\StepExecutor\ActionExecutor\ExecutorInterface;
use Ibexa\Migration\Tests\StepExecutor\AbstractStepExecutorTest;
use Ibexa\Segmentation\StepExecutor\SegmentCreateStepExecutor;
use Ibexa\Segmentation\Value\SegmentGroupCreateStruct;
use Ibexa\Segmentation\Value\SegmentGroupMatcher;
use Ibexa\Segmentation\Value\Step\SegmentCreateStep;

/**
 * @covers \Ibexa\Segmentation\StepExecutor\SegmentCreateStepExecutor
 */
final class SegmentCreateStepExecutorTest extends AbstractStepExecutorTest
{
    private SegmentCreateStepExecutor $executor;

    private SegmentationServiceInterface $segmentationService;

    public function setUp(): void
    {
        self::bootKernel();

        $this->segmentationService = self::getServiceByClassName(SegmentationServiceInterface::class);
        $this->executor = new SegmentCreateStepExecutor(
            $this->segmentationService,
            $this->createMock(ExecutorInterface::class),
        );
        self::configureExecutor($this->executor);

        self::setAdministratorUser();
    }

    public function testCreatingSegment(): void
    {
        $segmentGroup = $this->segmentationService->createSegmentGroup(new SegmentGroupCreateStruct([
            'identifier' => 'segment_group_foo',
            'name' => 'SEGMENT GROUP foo NAME',
        ]));

        $segments = $this->segmentationService->loadSegmentsAssignedToGroup($segmentGroup);

        self::assertCount(0, $segments);

        $step = new SegmentCreateStep(
            'foo identifier',
            'foo name',
            new SegmentGroupMatcher(null, 'segment_group_foo'),
        );

        $this->executor->handle($step);

        $segments = $this->segmentationService->loadSegmentsAssignedToGroup($segmentGroup);

        self::assertCount(1, $segments);
        $segment = $segments[0];
        self::assertSame('foo identifier', $segment->identifier);
        self::assertSame('foo name', $segment->name);
        self::assertSame('segment_group_foo', $segment->group->identifier);
    }
}
