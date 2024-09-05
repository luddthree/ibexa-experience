<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\CorporateAccount\Security;

use Ibexa\Bundle\CorporateAccount\IbexaCorporateAccountBundle;
use Ibexa\Bundle\CorporateAccount\Security\DeactivatedCompanyException;
use Ibexa\Bundle\CorporateAccount\Security\MemberChecker;
use Ibexa\Bundle\CorporateAccount\Security\MemberNotPartOfCompanyException;
use Ibexa\Bundle\CorporateAccount\Security\UserIsNotMemberException;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\CorporateAccount\Service\CompanyService;
use Ibexa\Contracts\CorporateAccount\Service\MemberService;
use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Core\FieldType\Checkbox\Value;
use Ibexa\Core\MVC\Symfony\Security\User;
use Ibexa\Core\MVC\Symfony\Security\UserChecker;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Core\MVC\Symfony\SiteAccessGroup;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Core\Repository\Values\User\User as APIUser;
use Ibexa\CorporateAccount\Configuration\CorporateAccountConfiguration;
use Ibexa\CorporateAccount\Values\MemberAssignment;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Throwable;

final class MemberCheckerTest extends TestCase
{
    private const EXAMPLE_USER_ID = 14;
    private const EXAMPLE_COMPANY_ID = 21;

    private MemberChecker $memberChecker;

    private UserChecker $userChecker;

    protected function setUp(): void
    {
        $this->userChecker = new UserChecker($this->createMock(UserService::class));

        $this->memberChecker = new MemberChecker(
            $this->userChecker,
            $this->getMemberServiceMock(),
            $this->getCompanyServiceMock(),
            $this->getCorporateAccountConfigurationMock(),
            $this->getSiteAccessServiceMock(),
            $this->getRepositoryMock()
        );
    }

    private function getCorporateAccountConfigurationMock(): CorporateAccountConfiguration
    {
        $corporateAccountConfigurationMock = $this->createMock(CorporateAccountConfiguration::class);

        $corporateAccountConfigurationMock
            ->method('getMemberContentTypeIdentifier')
            ->willReturn('member');

        return $corporateAccountConfigurationMock;
    }

    private function getRepositoryMock(): Repository
    {
        $repositoryMock = $this->createMock(Repository::class);

        $callback = static fn (callable $callable) => $callable($repositoryMock);
        $repositoryMock->method('sudo')->willReturnCallback($callback);

        return $repositoryMock;
    }

    private function getMemberServiceMock(): MemberService
    {
        $memberServiceMock = $this->createMock(MemberService::class);

        $memberServiceMock
            ->method('getMemberAssignmentsByMemberId')
            ->willReturn([
                new MemberAssignment(
                    1,
                    self::EXAMPLE_USER_ID,
                    'Company admin',
                    1,
                    self::EXAMPLE_COMPANY_ID,
                    1
                ),
            ]);

        return $memberServiceMock;
    }

    private function getCompanyServiceMock(bool $isActive = true): CompanyService
    {
        $companyServiceMock = $this->createMock(CompanyService::class);

        $companyServiceMock
            ->method('getCompany')
            ->with(self::EXAMPLE_COMPANY_ID)
            ->willReturn(new Company(
                new Content(
                    [
                        'versionInfo' => new VersionInfo(
                            [
                                'contentInfo' => new ContentInfo(
                                    [
                                        'id' => self::EXAMPLE_COMPANY_ID,
                                        'mainLanguageCode' => 'eng-GB',
                                    ]
                                ),
                            ]
                        ),
                        'internalFields' => [new Field(
                            [
                                'fieldDefIdentifier' => 'active',
                                'languageCode' => 'eng-GB',
                                'value' => new Value($isActive),
                            ]
                        )],
                    ]
                ),
            ));

        return $companyServiceMock;
    }

    private function getSiteAccessServiceMock(
        string $siteAccessName = 'corporate',
        string $siteAccessGroup = IbexaCorporateAccountBundle::CUSTOMER_PORTAL_GROUP_NAME
    ): SiteAccessServiceInterface {
        $siteAccessServiceMock = $this->createMock(SiteAccessServiceInterface::class);

        $corporateSiteAccess = new SiteAccess($siteAccessName);
        $corporateSiteAccess->groups[] = new SiteAccessGroup($siteAccessGroup);
        $siteAccessServiceMock
            ->method('getCurrent')
            ->willReturn($corporateSiteAccess);

        $siteAccessServiceMock
            ->method('get')
            ->with($siteAccessName)
            ->willReturn($corporateSiteAccess);

        return $siteAccessServiceMock;
    }

    private function getAPIUser(bool $enabled = true, string $contentType = 'member'): APIUser
    {
        return new APIUser(
            [
                'content' => new Content(
                    [
                        'versionInfo' => new VersionInfo(
                            [
                                'contentInfo' => new ContentInfo(
                                    [
                                        'id' => self::EXAMPLE_USER_ID,
                                    ]
                                ),
                            ]
                        ),
                        'contentType' => new ContentType([
                            'identifier' => $contentType,
                        ]),
                    ]
                ),
                'enabled' => $enabled,
            ]
        );
    }

    public function testCheckPreAuthWithEnabledUser(): void
    {
        try {
            $this->memberChecker->checkPreAuth(new User($this->getAPIUser()));
        } catch (Throwable $t) {
            self::fail('Error was not expected to be raised.');
        }
        $this->expectNotToPerformAssertions();
    }

    public function testCheckPreAuthWithDisabledUser(): void
    {
        $this->expectException(DisabledException::class);

        $this->memberChecker->checkPreAuth(new User($this->getAPIUser(false)));
    }

    public function testExecutionOnlyInCorporateSiteAccess(): void
    {
        $corporateAccountConfigurationMock = $this->createMock(CorporateAccountConfiguration::class);

        $corporateAccountConfigurationMock
            ->expects(self::never())
            ->method(self::anything());

        $memberChecker = new MemberChecker(
            $this->userChecker,
            $this->getMemberServiceMock(),
            $this->getCompanyServiceMock(),
            $corporateAccountConfigurationMock,
            $this->getSiteAccessServiceMock('non_corporate', 'non_corporate_group'),
            $this->getRepositoryMock()
        );

        $memberChecker->checkPreAuth(new User($this->getAPIUser()));
    }

    public function testAuthOnlyMembers(): void
    {
        $memberChecker = new MemberChecker(
            $this->userChecker,
            $this->getMemberServiceMock(),
            $this->getCompanyServiceMock(),
            $this->getCorporateAccountConfigurationMock(),
            $this->getSiteAccessServiceMock(),
            $this->getRepositoryMock()
        );

        $this->expectException(UserIsNotMemberException::class);
        $memberChecker->checkPreAuth(new User($this->getAPIUser(true, 'non_member')));
    }

    public function testAuthOnlyMembersInCompany(): void
    {
        $memberServiceMock = $this->createMock(MemberService::class);

        $memberServiceMock
            ->method('getMemberAssignmentsByMemberId')
            ->willReturn([]);

        $memberChecker = new MemberChecker(
            $this->userChecker,
            $memberServiceMock,
            $this->getCompanyServiceMock(),
            $this->getCorporateAccountConfigurationMock(),
            $this->getSiteAccessServiceMock(),
            $this->getRepositoryMock()
        );

        $this->expectException(MemberNotPartOfCompanyException::class);
        $memberChecker->checkPreAuth(new User($this->getAPIUser()));
    }

    public function testAuthMembersInDeactivatedCompany(): void
    {
        $memberChecker = new MemberChecker(
            $this->userChecker,
            $this->getMemberServiceMock(),
            $this->getCompanyServiceMock(false),
            $this->getCorporateAccountConfigurationMock(),
            $this->getSiteAccessServiceMock(),
            $this->getRepositoryMock()
        );

        $this->expectException(DeactivatedCompanyException::class);
        $memberChecker->checkPreAuth(new User($this->getAPIUser()));
    }
}
