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
use Ibexa\Segmentation\StepExecutor\SegmentUpdateStepExecutor;
use Ibexa\Segmentation\Value\Segment;
use Ibexa\Segmentation\Value\SegmentCreateStruct;
use Ibexa\Segmentation\Value\SegmentGroupCreateStruct;
use Ibexa\Segmentation\Value\SegmentMatcher;
use Ibexa\Segmentation\Value\Step\SegmentUpdateStep;

/**
 * @covers \Ibexa\Segmentation\StepExecutor\SegmentUpdateStepExecutor
 */
final class SegmentUpdateStepExecutorTest extends AbstractStepExecutorTest
{
    private SegmentUpdateStepExecutor $executor;

    private SegmentationServiceInterface $segmentationService;

    public function setUp(): void
    {
        self::bootKernel();

        $this->segmentationService = self::getServiceByClassName(SegmentationServiceInterface::class);
        $this->executor = new SegmentUpdateStepExecutor(
            $this->segmentationService,
            $this->createMock(ExecutorInterface::class),
        );
        self::configureExecutor($this->executor);

        self::setAdministratorUser();
    }

    public function testUpdatingSegment(): void
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

        $step = new SegmentUpdateStep(
            new SegmentMatcher(null, 'foo identifier'),
            'alpha identifier',
            'alpha name',
        );

        $this->executor->handle($step);

        $segments = $this->segmentationService->loadSegmentsAssignedToGroup($segmentGroup);

        self::assertCount(2, $segments);

        $segment = $this->findAlphaSegment($segments);
        self::assertSame('alpha identifier', $segment->identifier);
        self::assertSame('alpha name', $segment->name);
        self::assertSame('segment_group_foo', $segment->group->identifier);
    }

    /**
     * @param \Ibexa\Segmentation\Value\Segment[] $segments
     */
    private function findAlphaSegment(array $segments): Segment
    {
        foreach ($segments as $segment) {
            if ($segment->identifier === 'alpha identifier') {
                return $segment;
            }
        }

        self::fail('Unable to find Segment with identifier: "alpha identifier"');
    }
}
