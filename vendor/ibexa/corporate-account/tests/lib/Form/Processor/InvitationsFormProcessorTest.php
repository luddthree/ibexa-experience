<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\CorporateAccount\Form\Processor;

use Ibexa\Bundle\CorporateAccount\IbexaCorporateAccountBundle;
use Ibexa\ContentForms\Event\FormActionEvent;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\SubtreeLimitation;
use Ibexa\Contracts\Core\Repository\Values\User\UserGroup;
use Ibexa\Contracts\CorporateAccount\Exception\CorporateSiteAccessConfigurationException;
use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Contracts\User\Invitation\Invitation;
use Ibexa\Contracts\User\Invitation\InvitationCreateStruct;
use Ibexa\Contracts\User\Invitation\InvitationSender;
use Ibexa\Contracts\User\Invitation\InvitationService;
use Ibexa\Core\FieldType\Relation\Value;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\Location;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Core\Repository\Values\User\Role;
use Ibexa\CorporateAccount\Form\Data\Invitation\InviteMembersData;
use Ibexa\CorporateAccount\Form\Data\Invitation\SimpleInvitationData;
use Ibexa\CorporateAccount\Form\Processor\InvitationsFormProcessor;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormInterface;

final class InvitationsFormProcessorTest extends TestCase
{
    private const NON_CORPORATE_SITE_ACCESS_GROUP = 'non_corporate_sa';
    private const TEST_EMAIL = 'email@ibexa.co';

    /** @var \Ibexa\Contracts\Core\Repository\UserService&\PHPUnit\Framework\MockObject\MockObject */
    private UserService $userService;

    /** @var \Ibexa\Contracts\User\Invitation\InvitationService&\PHPUnit\Framework\MockObject\MockObject */
    private InvitationService $invitationService;

    /** @var \Ibexa\Contracts\User\Invitation\InvitationSender&\PHPUnit\Framework\MockObject\MockObject */
    private InvitationSender $sender;

    /** @var \Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface&\PHPUnit\Framework\MockObject\MockObject */
    private SiteAccessServiceInterface $siteAccessService;

    /** @var string[][] */
    private array $siteAccessGroups;

    private InvitationsFormProcessor $formProcessor;

    protected function setUp(): void
    {
        $this->userService = $this->createMock(UserService::class);
        $this->invitationService = $this->createMock(InvitationService::class);
        $this->sender = $this->createMock(InvitationSender::class);
        $this->siteAccessService = $this->createMock(SiteAccessServiceInterface::class);
        $this->siteAccessGroups = [
            self::NON_CORPORATE_SITE_ACCESS_GROUP => [
                'nc_site_access_1',
            ],
            IbexaCorporateAccountBundle::CUSTOMER_PORTAL_GROUP_NAME => [
                'customer_portal_1',
                'customer_portal_2',
            ],
        ];

        $this->formProcessor = new InvitationsFormProcessor(
            $this->userService,
            $this->invitationService,
            $this->sender,
            $this->siteAccessService,
            $this->siteAccessGroups
        );
    }

    /**
     * @return array<string, array{SiteAccess, SiteAccess|null}>
     */
    public function siteAccessProvider(): array
    {
        return [
            'current_in_corporate' => [
                 new SiteAccess('customer_portal_2'),
                 new SiteAccess('customer_portal_2'),
            ],
            'no_current' => [
                new SiteAccess('customer_portal_1'),
                null,
            ],
        ];
    }

    /**
     * @dataProvider siteAccessProvider
     */
    public function testWithSiteAccess(
        SiteAccess $expectedSiteAccess,
        ?SiteAccess $currentSiteAccess = null
    ): void {
        $this
            ->siteAccessService
            ->method('getCurrent')
            ->willReturn($currentSiteAccess);

        $company = $this->getCompany();

        $userGroup = $this->createMock(UserGroup::class);

        $this
            ->userService
            ->method('loadUserGroup')
            ->with($company->getMembersId())
            ->willReturn($userGroup);

        $role = new Role(['identifier' => 'test_role']);

        $data = new InviteMembersData();
        $data->setInvitations([new SimpleInvitationData(
            self::TEST_EMAIL,
            $role
        )]);

        $data = $this->getMembersData();

        $event = new FormActionEvent(
            $this->createMock(FormInterface::class),
            $data,
            'button',
            ['company' => $company]
        );

        $subtreeLimitation = new SubtreeLimitation(
            [
                'limitationValues' => ['1/2/3/4'],
            ]
        );

        $invitation = new Invitation(
            self::TEST_EMAIL,
            'hash',
            new \DateTime(),
            $expectedSiteAccess->name,
            false,
            $role,
            $userGroup,
            $subtreeLimitation
        );

        $this
            ->invitationService
            ->method('createInvitation')
            ->with(
                new InvitationCreateStruct(
                    self::TEST_EMAIL,
                    $expectedSiteAccess->name,
                    $userGroup,
                    $role,
                    $subtreeLimitation
                )
            )
            ->willReturn($invitation);

        $this
            ->sender
            ->method('sendInvitation')
            ->with($invitation);

        $this->formProcessor->processCreateAndSend($event);

        self::expectNotToPerformAssertions();
    }

    public function testInvalidSiteAccess(): void
    {
        $this
            ->siteAccessService
            ->method('getCurrent')
            ->willReturn(new SiteAccess('nc_site_access_1'));

        self::expectException(CorporateSiteAccessConfigurationException::class);
        $this->formProcessor->processCreateAndSend($this->getActionEvent());
    }

    public function testNoCorporateSiteAccess(): void
    {
        $this
            ->siteAccessService
            ->method('getCurrent')
            ->willReturn(null);

        $formProcessor = new InvitationsFormProcessor(
            $this->userService,
            $this->invitationService,
            $this->sender,
            $this->siteAccessService,
            [
                IbexaCorporateAccountBundle::CUSTOMER_PORTAL_GROUP_NAME => [],
            ]
        );

        self::expectException(CorporateSiteAccessConfigurationException::class);
        $formProcessor->processCreateAndSend($this->getActionEvent());
    }

    private function getCompany(): Company
    {
        return new Company(
            new Content([
                'versionInfo' => new VersionInfo([
                    'contentInfo' => new ContentInfo([
                        'mainLocation' => new Location([
                            'pathString' => '1/2/3/4',
                        ]),
                        'mainLanguageCode' => 'eng-GB',
                    ]),
                ]),
                'internalFields' => [
                    new Field(
                        [
                            'fieldDefIdentifier' => 'members',
                            'languageCode' => 'eng-GB',
                            'value' => new Value(44),
                        ]
                    ),
                ],
            ])
        );
    }

    private function getMembersData(): InviteMembersData
    {
        $data = new InviteMembersData();
        $data->setInvitations([
            new SimpleInvitationData(
                self::TEST_EMAIL,
                new Role(['identifier' => 'test_role'])
            ),
        ]);

        return $data;
    }

    private function getActionEvent(): FormActionEvent
    {
        return new FormActionEvent(
            $this->createMock(FormInterface::class),
            $this->getMembersData(),
            'button',
            ['company' => $this->getCompany()]
        );
    }
}
