<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Segmentation;

use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\Segmentation\SegmentationServiceDecorator;
use Ibexa\Contracts\Segmentation\SegmentationServiceInterface;
use Ibexa\Segmentation\Value\Segment;
use Ibexa\Segmentation\Value\SegmentCreateStruct;
use Ibexa\Segmentation\Value\SegmentGroup;
use Ibexa\Segmentation\Value\SegmentGroupCreateStruct;
use Ibexa\Segmentation\Value\SegmentGroupUpdateStruct;
use Ibexa\Segmentation\Value\SegmentUpdateStruct;
use PHPUnit\Framework\TestCase;

final class SegmentationServiceDecoratorTest extends TestCase
{
    private const EXAMPLE_SEGMENT_ID = 1;
    private const EXAMPLE_IDENTIFIER = 'foo';
    private const EXAMPLE_SEGMENT_GROUP_ID = 2;

    /** @var \Ibexa\Contracts\Segmentation\SegmentationServiceInterface&\PHPUnit\Framework\MockObject\MockObject */
    private SegmentationServiceInterface $service;

    private SegmentationServiceDecorator $decorator;

    protected function setUp(): void
    {
        $this->service = $this->createMock(SegmentationServiceDecorator::class);
        $this->decorator = $this->createDecorator($this->service);
    }

    public function testLoadSegment(): void
    {
        $expectedResult = $this->createMock(Segment::class);

        $this->service
            ->expects(self::once())
            ->method('loadSegment')
            ->with(self::EXAMPLE_SEGMENT_ID)
            ->willReturn($expectedResult)
        ;

        $actualResult = $this->decorator->loadSegment(self::EXAMPLE_SEGMENT_ID);

        self::assertEquals($expectedResult, $actualResult);
    }

    public function testLoadSegmentByIdentifier(): void
    {
        $expectedResult = $this->createMock(Segment::class);

        $this->service
            ->expects(self::once())
            ->method('loadSegmentByIdentifier')
            ->with(self::EXAMPLE_IDENTIFIER)
            ->willReturn($expectedResult)
        ;

        $actualResult = $this->decorator->loadSegmentByIdentifier(self::EXAMPLE_IDENTIFIER);

        self::assertEquals($expectedResult, $actualResult);
    }

    public function testCreateSegment(): void
    {
        $createStruct = $this->createMock(SegmentCreateStruct::class);
        $expectedResult = $this->createMock(Segment::class);

        $this->service
            ->expects(self::once())
            ->method('createSegment')
            ->with($createStruct)
            ->willReturn($expectedResult)
        ;

        $actualResult = $this->decorator->createSegment($createStruct);

        self::assertEquals($expectedResult, $actualResult);
    }

    public function testUpdateSegment(): void
    {
        $segment = $this->createMock(Segment::class);
        $updateStruct = $this->createMock(SegmentUpdateStruct::class);
        $expectedResult = $this->createMock(Segment::class);

        $this->service
            ->expects(self::once())
            ->method('updateSegment')
            ->with($segment, $updateStruct)
            ->willReturn($expectedResult)
        ;

        $actualResult = $this->decorator->updateSegment($segment, $updateStruct);

        self::assertEquals($expectedResult, $actualResult);
    }

    public function testRemoveSegment(): void
    {
        $segment = $this->createMock(Segment::class);
        $this->service
            ->expects(self::once())
            ->method('removeSegment')
            ->with($segment)
        ;

        $this->decorator->removeSegment($segment);
    }

    public function testLoadSegmentsAssignedToGroup(): void
    {
        $segmentGroup = $this->createMock(SegmentGroup::class);
        $expectedResult = [
            $this->createMock(Segment::class),
            $this->createMock(Segment::class),
            $this->createMock(Segment::class),
        ];

        $this->service
            ->expects(self::once())
            ->method('loadSegmentsAssignedToGroup')
            ->with($segmentGroup)
            ->willReturn($expectedResult)
        ;

        $actualResult = $this->decorator->loadSegmentsAssignedToGroup($segmentGroup);

        self::assertEquals($expectedResult, $actualResult);
    }

    public function testLoadSegmentGroup(): void
    {
        $expectedResult = $this->createMock(SegmentGroup::class);

        $this->service
            ->expects(self::once())
            ->method('loadSegmentGroup')
            ->with(self::EXAMPLE_SEGMENT_GROUP_ID)
            ->willReturn($expectedResult)
        ;

        $actualResult = $this->decorator->loadSegmentGroup(self::EXAMPLE_SEGMENT_GROUP_ID);

        self::assertEquals($expectedResult, $actualResult);
    }

    public function testLoadSegmentGroupByIdentifier(): void
    {
        $expectedResult = $this->createMock(SegmentGroup::class);

        $this->service
            ->expects(self::once())
            ->method('loadSegmentGroupByIdentifier')
            ->with(self::EXAMPLE_IDENTIFIER)
            ->willReturn($expectedResult)
        ;

        $actualResult = $this->decorator->loadSegmentGroupByIdentifier(self::EXAMPLE_IDENTIFIER);

        self::assertEquals($expectedResult, $actualResult);
    }

    public function testCreateSegmentGroup(): void
    {
        $createStruct = $this->createMock(SegmentGroupCreateStruct::class);
        $expectedResult = $this->createMock(SegmentGroup::class);

        $this->service
            ->expects(self::once())
            ->method('createSegmentGroup')
            ->with($createStruct)
            ->willReturn($expectedResult)
        ;

        $actualResult = $this->decorator->createSegmentGroup($createStruct);

        self::assertEquals($expectedResult, $actualResult);
    }

    public function testUpdateSegmentGroup(): void
    {
        $segmentGroup = $this->createMock(SegmentGroup::class);
        $updateStruct = $this->createMock(SegmentGroupUpdateStruct::class);
        $expectedResult = $this->createMock(SegmentGroup::class);

        $this->service
            ->expects(self::once())
            ->method('updateSegmentGroup')
            ->with($segmentGroup, $updateStruct)
            ->willReturn($expectedResult)
        ;

        $actualResult = $this->decorator->updateSegmentGroup($segmentGroup, $updateStruct);

        self::assertEquals($expectedResult, $actualResult);
    }

    public function testRemoveSegmentGroup(): void
    {
        $segmentGroup = $this->createMock(SegmentGroup::class);
        $this->service
            ->expects(self::once())
            ->method('removeSegmentGroup')
            ->with($segmentGroup)
        ;

        $this->decorator->removeSegmentGroup($segmentGroup);
    }

    public function testLoadSegmentGroups(): void
    {
        $expectedResult = [
            $this->createMock(SegmentGroup::class),
            $this->createMock(SegmentGroup::class),
            $this->createMock(SegmentGroup::class),
        ];

        $this->service
            ->expects(self::once())
            ->method('loadSegmentGroups')
            ->willReturn($expectedResult)
        ;

        $actualResult = $this->decorator->loadSegmentGroups();

        self::assertEquals($expectedResult, $actualResult);
    }

    public function testLoadSegmentsAssignedToUser(): void
    {
        $user = $this->createMock(User::class);
        $expectedResult = [
            $this->createMock(Segment::class),
            $this->createMock(Segment::class),
            $this->createMock(Segment::class),
        ];

        $this->service
            ->expects(self::once())
            ->method('loadSegmentsAssignedToUser')
            ->with($user)
            ->willReturn($expectedResult)
        ;

        $actualResult = $this->decorator->loadSegmentsAssignedToUser($user);

        self::assertEquals($expectedResult, $actualResult);
    }

    public function testLoadSegmentsAssignedToCurrentUser(): void
    {
        $expectedResult = [
            $this->createMock(Segment::class),
            $this->createMock(Segment::class),
            $this->createMock(Segment::class),
        ];

        $this->service
            ->expects(self::once())
            ->method('loadSegmentsAssignedToCurrentUser')
            ->willReturn($expectedResult)
        ;

        $actualResult = $this->decorator->loadSegmentsAssignedToCurrentUser();

        self::assertEquals($expectedResult, $actualResult);
    }

    public function testIsUserAssignedToSegment(): void
    {
        $user = $this->createMock(User::class);
        $segment = $this->createMock(Segment::class);
        $expectedResult = true;

        $this->service
            ->expects(self::once())
            ->method('isUserAssignedToSegment')
            ->with($user, $segment)
            ->willReturn($expectedResult)
        ;

        $actualResult = $this->decorator->isUserAssignedToSegment($user, $segment);

        self::assertEquals($expectedResult, $actualResult);
    }

    public function testAssignUserToSegment(): void
    {
        $user = $this->createMock(User::class);
        $segment = $this->createMock(Segment::class);
        $this->service
            ->expects(self::once())
            ->method('assignUserToSegment')
            ->with($user, $segment)
        ;

        $this->decorator->assignUserToSegment($user, $segment);
    }

    public function testUnassignUserFromSegment(): void
    {
        $user = $this->createMock(User::class);
        $segment = $this->createMock(Segment::class);
        $this->service
            ->expects(self::once())
            ->method('unassignUserFromSegment')
            ->with($user, $segment)
        ;

        $this->decorator->unassignUserFromSegment($user, $segment);
    }

    private function createDecorator(SegmentationServiceInterface $service): SegmentationServiceDecorator
    {
        return new class($service) extends SegmentationServiceDecorator {
        };
    }
}
