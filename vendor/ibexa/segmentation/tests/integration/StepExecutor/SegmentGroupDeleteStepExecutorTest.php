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
use Ibexa\Segmentation\StepExecutor\SegmentGroupDeleteStepExecutor;
use Ibexa\Segmentation\Value\SegmentGroupCreateStruct;
use Ibexa\Segmentation\Value\SegmentGroupMatcher;
use Ibexa\Segmentation\Value\Step\SegmentGroupDeleteStep;

/**
 * @covers \Ibexa\Segmentation\StepExecutor\SegmentGroupDeleteStepExecutor
 */
final class SegmentGroupDeleteStepExecutorTest extends AbstractStepExecutorTest
{
    private SegmentGroupDeleteStepExecutor $executor;

    private SegmentationServiceInterface $segmentationService;

    public function setUp(): void
    {
        self::bootKernel();

        $this->segmentationService = self::getServiceByClassName(SegmentationServiceInterface::class);
        $this->executor = new SegmentGroupDeleteStepExecutor(
            $this->segmentationService,
        );
        self::configureExecutor($this->executor);

        self::setAdministratorUser();
    }

    public function testDeletingSegmentGroup(): void
    {
        $segmentGroup = $this->segmentationService->createSegmentGroup(new SegmentGroupCreateStruct([
            'identifier' => 'segment_group_foo',
            'name' => 'SEGMENT GROUP foo NAME',
        ]));

        // Check reachability
        $this->segmentationService->loadSegmentGroup($segmentGroup->id);

        $step = new SegmentGroupDeleteStep(
            new SegmentGroupMatcher($segmentGroup->id),
        );

        $this->executor->handle($step);

        $this->expectException(NotFoundException::class);
        $this->segmentationService->loadSegmentGroup($segmentGroup->id);
    }
}
