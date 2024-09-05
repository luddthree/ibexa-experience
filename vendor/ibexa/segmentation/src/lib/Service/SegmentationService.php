<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\Service;

use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\HttpCache\PurgeClient\PurgeClientInterface;
use Ibexa\Contracts\Segmentation\SegmentationServiceInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Base\Exceptions\UnauthorizedException;
use Ibexa\Segmentation\Loader\UserSegmentsLoadStrategyInterface;
use Ibexa\Segmentation\Persistence\Handler\HandlerInterface;
use Ibexa\Segmentation\Service\Mapper\DomainMapper;
use Ibexa\Segmentation\Value\Persistence;
use Ibexa\Segmentation\Value\Segment;
use Ibexa\Segmentation\Value\SegmentCreateStruct;
use Ibexa\Segmentation\Value\SegmentGroup;
use Ibexa\Segmentation\Value\SegmentGroupCreateStruct;
use Ibexa\Segmentation\Value\SegmentGroupUpdateStruct;
use Ibexa\Segmentation\Value\SegmentUpdateStruct;
use function is_int;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SegmentationService implements SegmentationServiceInterface
{
    /** @var \Ibexa\Segmentation\Persistence\Handler\HandlerInterface */
    private $handler;

    /** @var \Ibexa\Segmentation\Service\Mapper\DomainMapper */
    private $domainMapper;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    private $userService;

    /** @var \Ibexa\Contracts\HttpCache\PurgeClient\PurgeClientInterface */
    private $purgeClient;

    /** @var \Ibexa\Segmentation\Loader\UserSegmentsLoadStrategyInterface */
    private $userSegmentsLoadStrategy;

    private ValidatorInterface $validator;

    public function __construct(
        HandlerInterface $handler,
        DomainMapper $domainMapper,
        PermissionResolver $permissionResolver,
        UserService $userService,
        PurgeClientInterface $purgeClient,
        UserSegmentsLoadStrategyInterface $userSegmentsLoadStrategy,
        ValidatorInterface $validator
    ) {
        $this->handler = $handler;
        $this->domainMapper = $domainMapper;
        $this->permissionResolver = $permissionResolver;
        $this->userService = $userService;
        $this->purgeClient = $purgeClient;
        $this->userSegmentsLoadStrategy = $userSegmentsLoadStrategy;
        $this->validator = $validator;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ForbiddenException
     */
    public function loadSegment(int $segmentId): Segment
    {
        $persistenceSegment = $this->handler->loadSegmentById($segmentId);
        $segmentGroup = $this->handler->loadSegmentGroupById($persistenceSegment->groupId);

        $segment = $this->domainMapper->createSegmentFromPersistenceObject(
            $persistenceSegment,
            $segmentGroup
        );

        if (!$this->permissionResolver->canUser('segment', 'read', $segment)) {
            throw new UnauthorizedException('segment', 'read', ['segmentId' => $segmentId]);
        }

        return $segment;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ForbiddenException
     */
    public function loadSegmentByIdentifier(string $identifier): Segment
    {
        $persistenceSegment = $this->handler->loadSegmentByIdentifier($identifier);
        $segmentGroup = $this->handler->loadSegmentGroupById($persistenceSegment->groupId);

        $segment = $this->domainMapper->createSegmentFromPersistenceObject(
            $persistenceSegment,
            $segmentGroup
        );

        if (!$this->permissionResolver->canUser('segment', 'read', $segment)) {
            throw new UnauthorizedException('segment', 'read', ['segmentIdentifier' => $identifier]);
        }

        return $segment;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ForbiddenException
     */
    public function createSegment(SegmentCreateStruct $createStruct): Segment
    {
        if (!$this->permissionResolver->canUser('segment', 'create', $createStruct)) {
            throw new UnauthorizedException('segment', 'create', [
                'segmentIdentifier' => $createStruct->identifier,
                'segmentGroupIdentifier' => $createStruct->group->identifier,
            ]);
        }

        $validationErrors = $this->validator->validate($createStruct);

        if ($validationErrors->count() > 0) {
            throw new InvalidArgumentException(
                '$createStruct',
                (string) $validationErrors->get(0)->getMessage()
            );
        }

        $persistenceCreateStruct = new Persistence\SegmentCreateStruct([
            'identifier' => $createStruct->identifier,
            'name' => $createStruct->name,
            'groupId' => $createStruct->group->id,
        ]);

        $this->handler->createSegment($persistenceCreateStruct);
        $persistenceSegment = $this->handler->loadSegmentByIdentifier($createStruct->identifier);
        $persistenceSegmentGroup = $this->handler->loadSegmentGroupById($persistenceSegment->groupId);

        return $this->domainMapper->createSegmentFromPersistenceObject($persistenceSegment, $persistenceSegmentGroup);
    }

    /**
     * @param \Ibexa\Segmentation\Value\Segment $segment
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ForbiddenException
     */
    public function updateSegment($segment, SegmentUpdateStruct $updateStruct): Segment
    {
        if (is_int($segment)) {
            @trigger_error(
                'Passing ID as a first argument is deprecated since Ibexa DXP 3.3 and will be dropped in 5.0. '
                . 'Pass ' . Segment::class . ' object instead.',
                E_USER_DEPRECATED
            );

            $segment = $this->loadSegment($segment);
        }

        if (!$segment instanceof Segment) {
            throw new InvalidArgumentException(
                '$segment',
                sprintf('Argument $segment has to be of type %s', Segment::class)
            );
        }

        $oldIdentifier = $segment->identifier;

        if (!$this->permissionResolver->canUser('segment', 'update', $updateStruct)) {
            throw new UnauthorizedException('segment', 'update', [
                'segmentIdentifier' => $updateStruct->identifier,
                'segmentGroupIdentifier' => $updateStruct->group->identifier,
            ]);
        }

        $persistenceSegment = $this->domainMapper->createPersistenceObjectFromSegment($segment);
        $persistenceUpdateStruct = new Persistence\SegmentUpdateStruct([
            'identifier' => $updateStruct->identifier,
            'name' => $updateStruct->name,
            'groupId' => $updateStruct->group->id,
        ]);

        $updatedPersistenceSegment = $this->handler->updateSegment($persistenceSegment, $persistenceUpdateStruct);
        $persistenceSegmentGroup = $this->handler->loadSegmentGroupById($updatedPersistenceSegment->groupId);

        // @todo move to separate layer
        $this->purgeClient->purge(['sg' . $persistenceSegment->id]);

        return $this->domainMapper->createSegmentFromPersistenceObject($updatedPersistenceSegment, $persistenceSegmentGroup);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ForbiddenException
     */
    public function removeSegment(Segment $segment): void
    {
        if (!$this->permissionResolver->canUser('segment', 'remove', $segment)) {
            throw new UnauthorizedException(
                'segment',
                'remove',
                [
                    'segmentId' => $segment->id,
                    'segmentIdentifier' => $segment->identifier,
                ]
            );
        }

        $this->handler->removeSegment(
            $this->domainMapper->createPersistenceObjectFromSegment($segment)
        );

        // @todo move to separate layer
        $this->purgeClient->purge(['sg' . $segment->id]);
    }

    public function loadSegmentsAssignedToGroup(SegmentGroup $segmentGroup): array
    {
        $persistenceGroup = $this->handler->loadSegmentGroupByIdentifier($segmentGroup->identifier);
        $segments = [];
        $persistenceSegments = $this->handler->loadSegmentsAssignedToGroup(
            $this->domainMapper->createPersistenceObjectFromSegmentGroup($segmentGroup)
        );
        foreach ($persistenceSegments as $persistenceSegment) {
            $segment = $this->domainMapper->createSegmentFromPersistenceObject($persistenceSegment, $persistenceGroup);

            if (!$this->permissionResolver->canUser('segment', 'read', $segment)) {
                continue;
            }

            $segments[] = $segment;
        }

        return $segments;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ForbiddenException
     */
    public function loadSegmentGroup(int $segmentGroupId): SegmentGroup
    {
        $segmentGroup = $this->domainMapper->createSegmentGroupFromPersistenceObject(
            $this->handler->loadSegmentGroupById($segmentGroupId)
        );

        if (!$this->permissionResolver->hasAccess('segment_group', 'read')) {
            throw new UnauthorizedException('segment_group', 'read', ['segmentGroupId' => $segmentGroupId]);
        }

        return $segmentGroup;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ForbiddenException
     */
    public function loadSegmentGroupByIdentifier(string $identifier): SegmentGroup
    {
        $segmentGroup = $this->domainMapper->createSegmentGroupFromPersistenceObject(
            $this->handler->loadSegmentGroupByIdentifier($identifier)
        );

        if (!$this->permissionResolver->hasAccess('segment_group', 'read')) {
            throw new UnauthorizedException('segment_group', 'read', ['segmentGroupIdentifier' => $identifier]);
        }

        return $segmentGroup;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ForbiddenException
     */
    public function createSegmentGroup(SegmentGroupCreateStruct $createStruct): SegmentGroup
    {
        if (!$this->permissionResolver->hasAccess('segment_group', 'create')) {
            throw new UnauthorizedException('segment_group', 'create', [
                'segmentGroupIdentifier' => $createStruct->identifier,
            ]);
        }

        $validationErrors = $this->validator->validate($createStruct);

        if ($validationErrors->count() > 0) {
            throw new InvalidArgumentException(
                '$createStruct',
                (string) $validationErrors->get(0)->getMessage()
            );
        }

        $persistenceCreateStruct = new Persistence\SegmentGroupCreateStruct([
            'identifier' => $createStruct->identifier,
            'name' => $createStruct->name,
        ]);

        $persistenceSegmentGroup = $this->handler->createSegmentGroup($persistenceCreateStruct);

        foreach ($createStruct->createSegments as $segmentCreateStruct) {
            $persistenceSegmentCreateStruct = new Persistence\SegmentCreateStruct([
                'identifier' => $segmentCreateStruct->identifier,
                'name' => $segmentCreateStruct->name,
                'groupId' => $persistenceSegmentGroup->id,
            ]);
            $this->handler->createSegment($persistenceSegmentCreateStruct);
        }

        return $this->domainMapper->createSegmentGroupFromPersistenceObject($persistenceSegmentGroup);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ForbiddenException
     */
    public function updateSegmentGroup($segmentGroup, SegmentGroupUpdateStruct $updateStruct): SegmentGroup
    {
        if (is_int($segmentGroup)) {
            @trigger_error(
                'Passing ID as a first argument is deprecated since Ibexa DXP 3.3 and will be dropped in 5.0. '
                . 'Pass ' . SegmentGroup::class . ' object instead.',
                E_USER_DEPRECATED
            );

            $segmentGroup = $this->loadSegmentGroup($segmentGroup);
        }

        if (!$segmentGroup instanceof SegmentGroup) {
            throw new InvalidArgumentException(
                '$segmentGroup',
                sprintf('Argument $segmentGroup has to be of type %s', SegmentGroup::class)
            );
        }

        if (!$this->permissionResolver->hasAccess('segment_group', 'update')) {
            throw new UnauthorizedException('segment_group', 'update', [
                'segmentGroupIdentifier' => $updateStruct->identifier,
            ]);
        }

        $persistenceSegmentGroup = $this->domainMapper->createPersistenceObjectFromSegmentGroup($segmentGroup);
        $persistenceUpdateStruct = new Persistence\SegmentGroupUpdateStruct([
            'identifier' => $updateStruct->identifier,
            'name' => $updateStruct->name,
        ]);

        $persistenceSegmentGroup = $this->handler->updateSegmentGroup(
            $persistenceSegmentGroup,
            $persistenceUpdateStruct
        );

        return $this->domainMapper->createSegmentGroupFromPersistenceObject($persistenceSegmentGroup);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ForbiddenException
     */
    public function removeSegmentGroup(SegmentGroup $segmentGroup): void
    {
        if (!$this->permissionResolver->hasAccess('segment_group', 'remove')) {
            throw new UnauthorizedException(
                'segment_group',
                'remove',
                [
                    'segmentGroupId' => $segmentGroup->id,
                    'segmentGroupIdentifier' => $segmentGroup->identifier,
                ]
            );
        }

        $persistenceSegmentGroup = $this->domainMapper->createPersistenceObjectFromSegmentGroup($segmentGroup);

        // @todo move to separate layer
        $segments = $this->handler->loadSegmentsAssignedToGroup($persistenceSegmentGroup);
        $segmentTags = array_map(static function (Persistence\Segment $segment): string {
            return 'sg' . $segment->id;
        }, $segments);
        $this->purgeClient->purge($segmentTags);

        $this->handler->removeSegmentGroup($persistenceSegmentGroup);
    }

    public function loadSegmentGroups(): array
    {
        $segmentGroups = array_map(
            [$this->domainMapper, 'createSegmentGroupFromPersistenceObject'],
            $this->handler->loadSegmentGroups()
        );

        $segmentGroups = array_filter($segmentGroups, function (SegmentGroup $segmentGroup) {
            return $this->permissionResolver->canUser('segment_group', 'read', $segmentGroup);
        });

        return $segmentGroups;
    }

    public function loadSegmentsAssignedToUser(User $user): array
    {
        $groups = [];
        $segments = [];
        $persistenceSegments = $this->userSegmentsLoadStrategy->loadUserAssignedSegments($user);
        foreach ($persistenceSegments as $persistenceSegment) {
            $groupId = $persistenceSegment->groupId;
            if (!isset($groups[$groupId])) {
                $groups[$groupId] = $this->handler->loadSegmentGroupById($groupId);
            }

            $segment = $this->domainMapper->createSegmentFromPersistenceObject($persistenceSegment, $groups[$groupId]);

            if (!$this->permissionResolver->canUser('segment', 'read', $segment)) {
                continue;
            }

            $segments[] = $segment;
        }

        return $segments;
    }

    public function loadSegmentsAssignedToCurrentUser(): array
    {
        $currentUserId = $this->permissionResolver->getCurrentUserReference()->getUserId();
        $currentUser = $this->userService->loadUser($currentUserId);

        return $this->loadSegmentsAssignedToUser($currentUser);
    }

    public function isUserAssignedToSegment(User $user, Segment $segment): bool
    {
        return $this->handler->isUserAssignedToSegment(
            $user->id,
            $this->domainMapper->createPersistenceObjectFromSegment($segment)
        );
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ForbiddenException
     */
    public function assignUserToSegment(User $user, Segment $segment): void
    {
        if (!$this->permissionResolver->canUser('segment', 'assign_to_user', $segment, [$user])) {
            throw new UnauthorizedException(
                'segment',
                'assign_to_user',
                [
                    'segmentId' => $segment->id,
                    'segmentIdentifier' => $segment->identifier,
                    'userId' => $user->getUserId(),
                ]
            );
        }

        $this->handler->assignUserToSegment(
            $user->id,
            $this->domainMapper->createPersistenceObjectFromSegment($segment)
        );

        // @todo move to separate layer
        $this->purgeClient->purge(['sg' . $segment->id]);

        // @todo fix assign
        $this->purgeClient->purge(['tb']);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ForbiddenException
     */
    public function unassignUserFromSegment(User $user, Segment $segment): void
    {
        if (!$this->permissionResolver->canUser('segment', 'assign_to_user', $segment, [$user])) {
            throw new UnauthorizedException(
                'segment',
                'assign_to_user',
                [
                    'segmentIdentifier' => $segment->identifier,
                    'userId' => $user->getUserId(),
                ]
            );
        }

        $this->handler->unassignUserFromSegment(
            $user->id,
            $this->domainMapper->createPersistenceObjectFromSegment($segment)
        );

        // @todo move to separate layer
        $this->purgeClient->purge(['sg' . $segment->id]);

        // @todo fix unassign
        $this->purgeClient->purge(['tb']);
    }

    public function getUserSegmentsLoadStrategy(): UserSegmentsLoadStrategyInterface
    {
        return $this->userSegmentsLoadStrategy;
    }

    public function setUserSegmentsLoadStrategy(UserSegmentsLoadStrategyInterface $userSegmentsLoadStrategy): void
    {
        $this->userSegmentsLoadStrategy = $userSegmentsLoadStrategy;
    }
}

class_alias(SegmentationService::class, 'Ibexa\Platform\Segmentation\Service\SegmentationService');
