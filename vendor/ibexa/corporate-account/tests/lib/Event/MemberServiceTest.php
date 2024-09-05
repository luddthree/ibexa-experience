<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\CorporateAccount\Event;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Ibexa\Contracts\Core\Repository\Values\User\User;
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
use Ibexa\CorporateAccount\Event\MemberService;

final class MemberServiceTest extends AbstractServiceTest
{
    protected function getEventServiceClass(): string
    {
        return MemberService::class;
    }

    protected function getServiceInterface(): string
    {
        return MemberServiceInterface::class;
    }

    public function getTestServiceMethods(): array
    {
        $contentMock = self::createMock(Content::class);
        $member = new Member(
            self::createMock(User::class),
            new Company($contentMock),
            self::createMock(Role::class)
        );

        return [
            'test member update' => [
                'updateMember',
                [
                    BeforeUpdateMemberEvent::class => static function (BeforeUpdateMemberEvent $event) {},
                    UpdateMemberEvent::class => static function (UpdateMemberEvent $event) {},
                ],
                [
                    $member,
                    new MemberUpdateStruct(),
                ],
                $member,
            ],
            'test member create' => [
                'createMember',
                [
                    BeforeCreateMemberEvent::class => static function (BeforeCreateMemberEvent $event) {},
                    CreateMemberEvent::class => static function (CreateMemberEvent $event) {},
                ],
                [
                    new Company($contentMock),
                    new MemberCreateStruct(),
                    self::createMock(Role::class),
                ],
                $member,
            ],
            'test member delete' => [
                'deleteMember',
                [
                    BeforeDeleteMemberEvent::class => static function (BeforeDeleteMemberEvent $event) {},
                    DeleteMemberEvent::class => static function (DeleteMemberEvent $event) {},
                ],
                [
                    $member,
                ],
            ],
            'test set member role' => [
                'setMemberRole',
                [
                    BeforeSetMemberRoleEvent::class => static function (BeforeSetMemberRoleEvent $event) {},
                    SetMemberRoleEvent::class => static function (SetMemberRoleEvent $event) {},
                ],
                [
                    $member,
                    self::createMock(Role::class),
                ],
            ],
        ];
    }

    public function getInterruptedTestServiceMethods(): array
    {
        $contentMock = self::createMock(Content::class);
        $member = new Member(
            self::createMock(User::class),
            new Company($contentMock),
            self::createMock(Role::class)
        );
        $interruptedMemberReturn = new Member(
            self::createMock(User::class),
            new Company($contentMock),
            self::createMock(Role::class)
        );

        return [
            'test interrupted member update' => [
                'updateMember',
                [
                    BeforeUpdateMemberEvent::class => static function (BeforeUpdateMemberEvent $event) use ($interruptedMemberReturn) {
                        $event->setUpdatedMember($interruptedMemberReturn);
                        $event->stopPropagation();
                    },
                    UpdateMemberEvent::class => static function (UpdateMemberEvent $event) {
                        self::fail(
                            BeforeUpdateMemberEvent::class . ' with ::stopPropagation() called'
                            . ' should result in ' . UpdateMemberEvent::class . ' not being dispatched'
                        );
                    },
                ],
                [
                    $member,
                    new MemberUpdateStruct(),
                ],
                $member,
                $interruptedMemberReturn,
            ],
            'test interrupted member create' => [
                'createMember',
                [
                    BeforeCreateMemberEvent::class => static function (BeforeCreateMemberEvent $event) use ($interruptedMemberReturn) {
                        $event->setMember($interruptedMemberReturn);
                        $event->stopPropagation();
                    },
                    CreateMemberEvent::class => static function (CreateMemberEvent $event) {
                        self::fail(
                            BeforeCreateMemberEvent::class . ' with ::stopPropagation() called'
                            . ' should result in ' . CreateMemberEvent::class . ' not being dispatched'
                        );
                    },
                ],
                [
                    new Company($contentMock),
                    new MemberCreateStruct(),
                    self::createMock(Role::class),
                ],
                $member,
                $interruptedMemberReturn,
            ],
            'test interrupted member delete' => [
                'deleteMember',
                [
                    BeforeDeleteMemberEvent::class => static function (BeforeDeleteMemberEvent $event) {
                        $event->stopPropagation();
                    },
                    DeleteMemberEvent::class => static function (DeleteMemberEvent $event) {
                        self::fail(
                            BeforeDeleteMemberEvent::class . ' with ::stopPropagation() called'
                            . ' should result in ' . DeleteMemberEvent::class . ' not being dispatched'
                        );
                    },
                ],
                [
                    $member,
                ],
            ],
            'test interrupted set member role' => [
                'setMemberRole',
                [
                    BeforeSetMemberRoleEvent::class => static function (BeforeSetMemberRoleEvent $event) {
                        $event->stopPropagation();
                    },
                    SetMemberRoleEvent::class => static function (SetMemberRoleEvent $event) {
                        self::fail(
                            BeforeSetMemberRoleEvent::class . ' with ::stopPropagation() called'
                            . ' should result in ' . SetMemberRoleEvent::class . ' not being dispatched'
                        );
                    },
                ],
                [
                    $member,
                    self::createMock(Role::class),
                ],
            ],
        ];
    }
}
