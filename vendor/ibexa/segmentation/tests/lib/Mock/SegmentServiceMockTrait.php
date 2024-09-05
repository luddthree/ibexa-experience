<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Segmentation\Mock;

use Ibexa\Segmentation\Value\Segment;
use Ibexa\Segmentation\Value\SegmentGroup;
use PHPUnit\Framework\MockObject\MockObject;

trait SegmentServiceMockTrait
{
    /** @var \PHPUnit\Framework\MockObject\MockObject */
    protected $segmentationMock;

    /** @var \Ibexa\Segmentation\Value\SegmentGroup */
    protected $segmentGroup;

    /** @var \Ibexa\Segmentation\Value\Segment */
    protected $segment;

    final protected function setUp(): void
    {
        $this->segmentGroup = new SegmentGroup([
            'id' => 1,
            'identifier' => 'test',
            'name' => 'test',
        ]);

        $this->segment = new Segment([
            'id' => 2,
            'identifier' => 'test',
            'name' => 'test',
            'group' => $this->segmentGroup,
        ]);

        if ($this->segmentationMock instanceof MockObject) {
            $this->segmentationMock
                ->method('loadSegmentGroups')
                ->willReturn([$this->segmentGroup]);

            $this->segmentationMock
                ->method('loadSegmentsAssignedToGroup')
                ->with($this->segmentGroup)
                ->willReturn([$this->segment]);

            $this->segmentationMock
                ->method('loadSegmentGroup')
                ->with(1)
                ->willReturn($this->segmentGroup);

            $this->segmentationMock
                ->method('loadSegment')
                ->with(2)
                ->willReturn($this->segment);
        }
    }
}
