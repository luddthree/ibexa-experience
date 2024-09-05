<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\StepExecutor\ActionExecutor\Segment;

use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\Segmentation\SegmentationServiceInterface;
use Ibexa\Migration\ValueObject\Step\Action;
use Ibexa\Segmentation\StepExecutor\ActionExecutor\AbstractAssignToUser;
use Ibexa\Segmentation\Value\Segment;
use Ibexa\Segmentation\Value\Step\Action\Segment\AssignToUser as AssignToUserAction;
use Webmozart\Assert\Assert;

final class AssignToUser extends AbstractAssignToUser
{
    /** @var \Ibexa\Contracts\Segmentation\SegmentationServiceInterface */
    private $segmentationService;

    public function __construct(UserService $userService, SegmentationServiceInterface $segmentationService)
    {
        parent::__construct($userService);
        $this->segmentationService = $segmentationService;
    }

    public static function getExecutorKey(): string
    {
        return AssignToUserAction::TYPE;
    }

    /**
     * @param \Ibexa\Segmentation\Value\Step\Action\Segment\AssignToUser $action
     * @param \Ibexa\Segmentation\Value\Segment $valueObject
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\LimitationValidationException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function handle(Action $action, ValueObject $valueObject): void
    {
        Assert::isInstanceOf($action, AssignToUserAction::class);
        Assert::isInstanceOf($valueObject, Segment::class);

        $user = $this->getUser($action);
        if (!$this->segmentationService->isUserAssignedToSegment($user, $valueObject)) {
            $this->segmentationService->assignUserToSegment($user, $valueObject);
        }
    }
}
