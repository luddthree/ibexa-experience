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
use Ibexa\Segmentation\StepExecutor\SegmentGroupCreateStepExecutor;
use Ibexa\Segmentation\Value\Step\SegmentGroupCreateStep;

/**
 * @covers \Ibexa\Segmentation\StepExecutor\SegmentGroupCreateStepExecutor
 */
final class SegmentGroupCreateStepExecutorTest extends AbstractStepExecutorTest
{
    private SegmentGroupCreateStepExecutor $executor;

    private SegmentationServiceInterface $segmentationService;

    public function setUp(): void
    {
        self::bootKernel();

        $this->segmentationService = self::getServiceByClassName(SegmentationServiceInterface::class);
        $this->executor = new SegmentGroupCreateStepExecutor(
            $this->segmentationService,
        );
        self::configureExecutor($this->executor);

        self::setAdministratorUser();
    }

    public function testCreatingSegmentGroup(): void
    {
        try {
            $this->segmentationService->loadSegmentGroupByIdentifier('foo identifier');
            self::fail('Segment Group with identifier: "foo identifier" should not be reachable');
        } catch (NotFoundException $e) {
            // expected
        }

        $step = new SegmentGroupCreateStep(
            'foo identifier',
            'foo name',
        );

        $this->executor->handle($step);

        $segmentGroup = $this->segmentationService->loadSegmentGroupByIdentifier('foo identifier');
        self::assertSame('foo identifier', $segmentGroup->identifier);
        self::assertSame('foo name', $segmentGroup->name);
    }
}
