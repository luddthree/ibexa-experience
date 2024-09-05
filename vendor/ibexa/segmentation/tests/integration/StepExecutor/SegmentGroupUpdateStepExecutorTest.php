<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Segmentation\StepExecutor;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Segmentation\SegmentationServiceInterface;
use Ibexa\Migration\Tests\StepExecutor\AbstractStepExecutorTest;
use Ibexa\Segmentation\StepExecutor\SegmentGroupUpdateStepExecutor;
use Ibexa\Segmentation\Value\SegmentGroupCreateStruct;
use Ibexa\Segmentation\Value\SegmentGroupMatcher;
use Ibexa\Segmentation\Value\Step\SegmentGroupUpdateStep;

/**
 * @covers \Ibexa\Segmentation\StepExecutor\SegmentGroupUpdateStepExecutor
 */
final class SegmentGroupUpdateStepExecutorTest extends AbstractStepExecutorTest
{
    private SegmentGroupUpdateStepExecutor $executor;

    private SegmentationServiceInterface $segmentationService;

    public function setUp(): void
    {
        self::bootKernel();

        $this->segmentationService = self::getServiceByClassName(SegmentationServiceInterface::class);
        $this->executor = new SegmentGroupUpdateStepExecutor(
            $this->segmentationService,
        );
        self::configureExecutor($this->executor);

        self::setAdministratorUser();
    }

    public function testUpdatingSegmentGroup(): void
    {
        $segmentGroup = $this->segmentationService->createSegmentGroup(new SegmentGroupCreateStruct([
            'identifier' => 'segment_group_foo',
            'name' => 'SEGMENT GROUP foo NAME',
        ]));

        try {
            $this->segmentationService->loadSegmentGroupByIdentifier('foo identifier');
            self::fail('Segment Group with identifier: "foo identifier" should not be reachable');
        } catch (NotFoundException $e) {
            // expected
        }

        $step = new SegmentGroupUpdateStep(
            new SegmentGroupMatcher($segmentGroup->id),
            'foo identifier',
            'foo name',
        );

        $this->executor->handle($step);

        try {
            $this->segmentationService->loadSegmentGroupByIdentifier('segment_group_foo');
            self::fail('Segment Group with identifier: "segment_group_foo" should not be reachable');
        } catch (NotFoundException $e) {
            // expected
        }

        $segmentGroup = $this->segmentationService->loadSegmentGroupByIdentifier('foo identifier');
        self::assertSame('foo name', $segmentGroup->name);
    }
}
