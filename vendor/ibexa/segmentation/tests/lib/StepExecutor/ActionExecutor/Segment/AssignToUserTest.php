<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Segmentation\StepExecutor\ActionExecutor\Segment;

use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\Segmentation\SegmentationServiceInterface;
use Ibexa\Segmentation\StepExecutor\ActionExecutor\Segment\AssignToUser;
use Ibexa\Segmentation\Value\Step\Action\Segment\AssignToUser as AssignToUserAction;
use Ibexa\Tests\Segmentation\Mock\SegmentServiceMockTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Segmentation\StepExecutor\ActionExecutor\Segment\AssignToUser
 */
final class AssignToUserTest extends TestCase
{
    use SegmentServiceMockTrait {
        setUp as segmentSetUp;
    }

    /** @var \Ibexa\Segmentation\StepExecutor\ActionExecutor\Segment\AssignToUser */
    private $actionExecutor;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $userServiceMock;

    /** @var \Ibexa\Contracts\Core\Repository\Values\User\User */
    private $userMock;

    protected function setUp(): void
    {
        $this->segmentationMock = $this->createMock(SegmentationServiceInterface::class);
        $this->userMock = $this->createMock(User::class);

        $this->userServiceMock = $this->createMock(UserService::class);

        $this->actionExecutor = new AssignToUser($this->userServiceMock, $this->segmentationMock);

        $this->segmentSetUp();
    }

    public function testGetExecutorKey(): void
    {
        self::assertSame('assign_segment_to_user', AssignToUser::getExecutorKey());
    }

    public function testHandle(): void
    {
        $action = new AssignToUserAction(1);

        $this->userServiceMock->expects(self::once())
            ->method('loadUser')
            ->with(1)
            ->willReturn($this->userMock);

        $this->segmentationMock->expects(self::once())
            ->method('isUserAssignedToSegment')
            ->with($this->userMock, $this->segment)
            ->willReturn(false);

        $this->segmentationMock->expects(self::once())
            ->method('assignUserToSegment')
            ->with($this->userMock, $this->segment);

        $this->actionExecutor->handle($action, $this->segment);
    }
}
