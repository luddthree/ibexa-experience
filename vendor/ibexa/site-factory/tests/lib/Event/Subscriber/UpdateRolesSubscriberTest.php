<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\SiteFactory\Event\Subscriber;

use Ibexa\Contracts\Core\Repository\RoleService;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\SiteAccessLimitation;
use Ibexa\Contracts\SiteFactory\Events\CreateSiteEvent;
use Ibexa\Contracts\SiteFactory\Events\DeleteSiteEvent;
use Ibexa\Contracts\SiteFactory\Events\UpdateSiteEvent;
use Ibexa\Contracts\SiteFactory\Values\Site\PublicAccess;
use Ibexa\Contracts\SiteFactory\Values\Site\Site;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteCreateStruct;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteUpdateStruct;
use Ibexa\Core\Base\Exceptions\UnauthorizedException;
use Ibexa\Core\Repository\Values\User\Policy;
use Ibexa\Core\Repository\Values\User\PolicyDraft;
use Ibexa\Core\Repository\Values\User\PolicyUpdateStruct;
use Ibexa\Core\Repository\Values\User\Role;
use Ibexa\Core\Repository\Values\User\RoleDraft;
use Ibexa\SiteFactory\Event\Subscriber\UpdateRolesSubscriber;
use PHPUnit\Framework\TestCase;

final class UpdateRolesSubscriberTest extends TestCase
{
    private const LIMITATION_A = '7009532190';
    private const LIMITATION_B = '5399197001';
    private const ANONYMOUS_ROLE_IDENTIFIER = 'ANONYMOUS';
    private const EDITOR_ROLE_IDENTIFIER = 'EDITOR';
    private const PUBLIC_ACCESS_IDENTIFIER_A = 'identifierA';
    private const PUBLIC_ACCESS_IDENTIFIER_B = 'identifierB';
    private const PUBLIC_ACCESS_IDENTIFIER_C = 'identifierC';

    /** @var \Ibexa\SiteFactory\Event\Subscriber\UpdateRolesSubscriber */
    private $subscriber;

    /** @var \Ibexa\Contracts\Core\Repository\RoleService|\PHPUnit\Framework\MockObject\MockObject */
    private $roleService;

    /** @var \Ibexa\Core\Repository\Values\User\RoleDraft */
    private $anonymousRoleDraft;

    /** @var \Ibexa\Core\Repository\Values\User\Policy */
    private $policy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->policy = new Policy([
            'module' => 'user',
            'function' => 'login',
            'limitations' => [
                $this->getSiteAccessLimitation([self::LIMITATION_A, self::LIMITATION_B]),
            ],
        ]);

        $anonymousRolePolicies = [
            new PolicyDraft(['innerPolicy' => $this->policy]),
        ];

        $anonymousRole = new Role(['policies' => $anonymousRolePolicies]);
        $editorRole = new Role();

        $this->anonymousRoleDraft = new RoleDraft(['innerRole' => $anonymousRole]);
        $editorRoleDraft = new RoleDraft(['innerRole' => $editorRole]);

        $this->roleService = $this->createMock(RoleService::class);
        $this->roleService
            ->method('loadRoleByIdentifier')
            ->willReturnMap([
                [self::ANONYMOUS_ROLE_IDENTIFIER, $anonymousRole],
                [self::EDITOR_ROLE_IDENTIFIER, $editorRole],
            ]);
        $this->roleService
            ->method('createRoleDraft')
            ->willReturnMap([
                [$anonymousRole, $this->anonymousRoleDraft],
                [$editorRole, $editorRoleDraft],
            ]);

        $this->subscriber = new UpdateRolesSubscriber(
            $this->roleService,
            [self::ANONYMOUS_ROLE_IDENTIFIER, self::EDITOR_ROLE_IDENTIFIER]
        );
    }

    public function testOnSiteCreate(): void
    {
        $this->expectNotToPerformAssertions();

        $this->roleService
            ->method('newPolicyUpdateStruct')
            ->willReturn(new PolicyUpdateStruct());

        $expectedSiteAccessLimitation = $this->getSiteAccessLimitation(
            [
                self::LIMITATION_A,
                self::LIMITATION_B,
                crc32(self::PUBLIC_ACCESS_IDENTIFIER_A),
                crc32(self::PUBLIC_ACCESS_IDENTIFIER_B),
            ]
        );

        $this->roleService
            ->method('updatePolicyByRoleDraft')
            ->with(
                $this->anonymousRoleDraft,
                new PolicyDraft(['innerPolicy' => $this->policy]),
                new PolicyUpdateStruct(
                    [
                        'limitations' => [
                            'SiteAccess' => $expectedSiteAccessLimitation,
                        ],
                    ]
                )
            );

        $this->subscriber->onSiteCreate($this->getSiteCreateEvent());
    }

    public function testOnSiteCreateWillHandleException(): void
    {
        $this->roleService
            ->method('newPolicyUpdateStruct')
            ->willThrowException(
                new UnauthorizedException('module', 'policy')
            );

        $this->roleService
            ->expects(self::once())
            ->method('deleteRoleDraft')
            ->with($this->anonymousRoleDraft)
        ;

        $this->subscriber->onSiteCreate($this->getSiteCreateEvent());
    }

    public function testOnSiteDelete(): void
    {
        $this->expectNotToPerformAssertions();

        $this->roleService
            ->method('newPolicyUpdateStruct')
            ->willReturn(new PolicyUpdateStruct());

        $expectedSiteAccessLimitation = $this->getSiteAccessLimitation(
            [
                self::LIMITATION_A,
                self::LIMITATION_B,
            ]
        );

        $this->roleService
            ->method('updatePolicyByRoleDraft')
            ->with(
                $this->anonymousRoleDraft,
                new PolicyDraft(['innerPolicy' => $this->policy]),
                new PolicyUpdateStruct(
                    [
                        'limitations' => [
                            'SiteAccess' => $expectedSiteAccessLimitation,
                        ],
                    ]
                )
            );

        $this->subscriber->onSiteDelete($this->getSiteDeleteEvent());
    }

    public function testOnSiteDeleteWillHandleException(): void
    {
        $this->roleService
            ->method('newPolicyUpdateStruct')
            ->willThrowException(
                new UnauthorizedException('module', 'policy')
            );

        $this->roleService
            ->expects(self::once())
            ->method('deleteRoleDraft')
            ->with($this->anonymousRoleDraft)
        ;

        $this->subscriber->onSiteDelete($this->getSiteDeleteEvent());
    }

    public function testOnSiteUpdate(): void
    {
        $this->expectNotToPerformAssertions();

        $this->roleService
            ->method('newPolicyUpdateStruct')
            ->willReturn(new PolicyUpdateStruct());

        $expectedSiteAccessLimitation = $this->getSiteAccessLimitation(
            [
                self::LIMITATION_A,
                self::LIMITATION_B,
                crc32(self::PUBLIC_ACCESS_IDENTIFIER_C),
            ]
        );

        $this->roleService
            ->method('updatePolicyByRoleDraft')
            ->with(
                $this->anonymousRoleDraft,
                new PolicyDraft(['innerPolicy' => $this->policy]),
                new PolicyUpdateStruct(
                    [
                        'limitations' => [
                            'SiteAccess' => $expectedSiteAccessLimitation,
                        ],
                    ]
                )
            );

        $this->subscriber->onSiteUpdate($this->getSiteUpdateEvent());
    }

    public function testOnSiteUpdateWillHandleException(): void
    {
        $this->roleService
            ->method('newPolicyUpdateStruct')
            ->willThrowException(
                new UnauthorizedException('module', 'policy')
            );

        $this->roleService
            ->expects(self::once())
            ->method('deleteRoleDraft')
            ->with($this->anonymousRoleDraft)
        ;

        $this->subscriber->onSiteUpdate($this->getSiteUpdateEvent());
    }

    public function testSubscribedEvents(): void
    {
        self::assertSame(
            [
                CreateSiteEvent::class => ['onSiteCreate', 0],
                DeleteSiteEvent::class => ['onSiteDelete', 0],
                UpdateSiteEvent::class => ['onSiteUpdate', 0],
            ],
            $this->subscriber::getSubscribedEvents()
        );
    }

    /**
     * @param string[] $limitationValues
     */
    private function getSiteAccessLimitation(array $limitationValues = []): SiteAccessLimitation
    {
        $siteAccessLimitation = new SiteAccessLimitation();
        $siteAccessLimitation->limitationValues = $limitationValues;

        return $siteAccessLimitation;
    }

    private function getSiteCreateEvent(): CreateSiteEvent
    {
        $site = new Site();
        $siteCreateStruct = new SiteCreateStruct(
            'site_name',
            false,
            [
                $this->getPublicAccess(),
                $this->getPublicAccess(self::PUBLIC_ACCESS_IDENTIFIER_B),
            ],
            2,
            []
        );

        return new CreateSiteEvent(
            $site,
            $siteCreateStruct
        );
    }

    private function getSiteDeleteEvent(): DeleteSiteEvent
    {
        $site = new Site();
        $site->publicAccesses = [
            $this->getPublicAccess(),
            $this->getPublicAccess(self::PUBLIC_ACCESS_IDENTIFIER_B),
        ];

        return new DeleteSiteEvent(
            $site,
        );
    }

    private function getSiteUpdateEvent(): UpdateSiteEvent
    {
        $site = new Site();
        $site->publicAccesses = [
            $this->getPublicAccess(),
        ];

        $updatedSite = new Site();
        $updatedSite->publicAccesses = [
            $this->getPublicAccess(),
            $this->getPublicAccess(self::PUBLIC_ACCESS_IDENTIFIER_C),
        ];

        $siteUpdateStruct = new SiteUpdateStruct(
            'site_name',
            []
        );

        return new UpdateSiteEvent(
            $updatedSite,
            $site,
            $siteUpdateStruct
        );
    }

    private function getPublicAccess(
        string $identifier = self::PUBLIC_ACCESS_IDENTIFIER_A,
        int $siteId = null,
        string $siteAccessGroup = 'sa_group'
    ): PublicAccess {
        return new PublicAccess(
            $identifier,
            $siteId,
            $siteAccessGroup,
        );
    }
}

class_alias(UpdateRolesSubscriberTest::class, 'EzSystems\EzPlatformSiteFactory\Tests\Event\Subscriber\UpdateRolesSubscriberTest');
