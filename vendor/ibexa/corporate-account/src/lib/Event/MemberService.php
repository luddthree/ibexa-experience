<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Event;

use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Ibexa\Contracts\CorporateAccount\Event\Member\BeforeCreateMemberEvent;
use Ibexa\Contracts\CorporateAccount\Event\Member\BeforeDeleteMemberEvent;
use Ibexa\Contracts\CorporateAccount\Event\Member\BeforeSetMemberRoleEvent;
use Ibexa\Contracts\CorporateAccount\Event\Member\BeforeUpdateMemberEvent;
use Ibexa\Contracts\CorporateAccount\Event\Member\CreateMemberEvent;
use Ibexa\Contracts\CorporateAccount\Event\Member\DeleteMemberEvent;
use Ibexa\Contracts\CorporateAccount\Event\Member\SetMemberRoleEvent;
use Ibexa\Contracts\CorporateAccount\Event\Member\UpdateMemberEvent;
use Ibexa\Contracts\CorporateAccount\Service\MemberService as MemberServiceInterface;
use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Contracts\CorporateAccount\Values\Member;
use Ibexa\Contracts\CorporateAccount\Values\MemberCreateStruct;
use Ibexa\Contracts\CorporateAccount\Values\MemberUpdateStruct;
use Ibexa\CorporateAccount\Decorator\MemberService as MemberServiceDecorator;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class MemberService extends MemberServiceDecorator
{
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        MemberServiceInterface $innerService,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($innerService);

        $this->eventDispatcher = $eventDispatcher;
    }

    public function createMember(
        Company $company,
        MemberCreateStruct $memberCreateStruct,
        Role $role
    ): Member {
        $parameters = [$company, $memberCreateStruct, $role];

        $beforeEvent = new BeforeCreateMemberEvent(...$parameters);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getMember();
        }

        $member = $beforeEvent->hasMember()
            ? $beforeEvent->getMember()
            : $this->innerService->createMember(...$parameters);

        $this->eventDispatcher->dispatch(
            new CreateMemberEvent($member, ...$parameters)
        );

        return $member;
    }

    public function updateMember(
        Member $member,
        MemberUpdateStruct $memberUpdateStruct
    ): Member {
        $parameters = [$member, $memberUpdateStruct];

        $beforeEvent = new BeforeUpdateMemberEvent(...$parameters);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getUpdatedMember();
        }

        $updatedMember = $beforeEvent->hasUpdatedMember()
            ? $beforeEvent->getUpdatedMember()
            : $this->innerService->updateMember(...$parameters);

        $this->eventDispatcher->dispatch(
            new UpdateMemberEvent($updatedMember, ...$parameters)
        );

        return $updatedMember;
    }

    public function deleteMember(Member $member): void
    {
        $parameters = [$member];

        $beforeEvent = new BeforeDeleteMemberEvent(...$parameters);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->deleteMember(...$parameters);

        $this->eventDispatcher->dispatch(
            new DeleteMemberEvent(...$parameters)
        );
    }

    public function setMemberRole(
        Member $member,
        Role $role
    ): void {
        $parameters = [$member, $role];

        $beforeEvent = new BeforeSetMemberRoleEvent(...$parameters);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->setMemberRole(...$parameters);

        $this->eventDispatcher->dispatch(
            new SetMemberRoleEvent(...$parameters)
        );
    }
}
