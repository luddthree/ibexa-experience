<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Dashboard\SiteAccess;

use Ibexa\AdminUi\Siteaccess\SiteaccessPreviewVoterContext;
use Ibexa\AdminUi\Siteaccess\SiteaccessPreviewVoterInterface;
use Ibexa\Bundle\Core\ApiLoader\RepositoryConfigurationProvider;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Dashboard\SiteAccess\DashboardSiteAccessPreviewVoter;
use PHPUnit\Framework\TestCase;

final class DashboardSiteAccessPreviewVoterTest extends TestCase
{
    private const SITE_ACCESS = 'site';

    private const LANGUAGE_CODE_ENG = 'eng-GB';

    private const REMOTE_ID = '12345qazwsx';

    private const REPOSITORY_ALIAS = 'default';

    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface&\PHPUnit\Framework\MockObject\MockObject */
    private ConfigResolverInterface $configResolver;

    /** @var \Ibexa\Contracts\Core\Repository\LocationService|(\Ibexa\Contracts\Core\Repository\LocationService&\PHPUnit\Framework\MockObject\MockObject)|\PHPUnit\Framework\MockObject\MockObject */
    private LocationService $locationService;

    private SiteaccessPreviewVoterInterface $siteAccessPreviewVoter;

    /** @var \Ibexa\Bundle\Core\ApiLoader\RepositoryConfigurationProvider&\PHPUnit\Framework\MockObject\MockObject */
    private RepositoryConfigurationProvider $repositoryConfigurationProvider;

    protected function setUp(): void
    {
        $this->configResolver = $this->createMock(ConfigResolverInterface::class);
        $this->repositoryConfigurationProvider = $this->createMock(RepositoryConfigurationProvider::class);
        $this->locationService = $this->createMock(LocationService::class);

        $this->siteAccessPreviewVoter = new DashboardSiteAccessPreviewVoter(
            $this->configResolver,
            $this->repositoryConfigurationProvider,
            $this->locationService
        );
    }

    public function testVoteReturnFalseWhenRemoteIdNotFound(): void
    {
        $this->mockConfigResolverGetParameter(
            [
                ['dashboard.container_remote_id', null, null, self::REMOTE_ID],
            ]
        );

        self::assertFalse(
            $this->siteAccessPreviewVoter->vote($this->createSiteAccessPreviewVoterContext())
        );
    }

    public function testVoteReturnFalseWhenLocationNotFound(): void
    {
        $this->mockConfigResolverGetParameter(
            [
                ['repository', null, self::SITE_ACCESS, self::REPOSITORY_ALIAS],
                ['dashboard.container_remote_id', null, null, self::REMOTE_ID],
            ]
        );

        $this->mockLocationServiceLoadLocationByRemoteId(
            true,
            $this->mockLocation(10)
        );

        self::assertFalse(
            $this->siteAccessPreviewVoter->vote($this->createSiteAccessPreviewVoterContext())
        );
    }

    public function testVoteReturnTrue(): void
    {
        $this->mockLocationServiceLoadLocationByRemoteId(
            false,
            $this->mockLocation(10),
        );
        $this->mockConfigResolverGetParameter(
            [
                ['repository', null, self::SITE_ACCESS, self::REPOSITORY_ALIAS],
                ['dashboard.container_remote_id', null, null, self::REMOTE_ID],
                ['languages', null, self::SITE_ACCESS, [self::LANGUAGE_CODE_ENG]],
            ]
        );
        $this->mockRepositoryConfigurationProviderGetCurrentRepositoryAlias();

        self::assertTrue(
            $this->siteAccessPreviewVoter->vote($this->createSiteAccessPreviewVoterContext())
        );
    }

    private function createSiteAccessPreviewVoterContext(): SiteaccessPreviewVoterContext
    {
        return new SiteaccessPreviewVoterContext(
            $this->mockLocation(1, [1, 10]),
            $this->createMock(VersionInfo::class),
            self::SITE_ACCESS,
            self::LANGUAGE_CODE_ENG
        );
    }

    /**
     * @param int[] $path
     */
    private function mockLocation(
        int $locationId,
        array $path = []
    ): Location {
        $location = $this->createMock(Location::class);
        $location->method('getId')->willReturn($locationId);
        $location->method('getPath')->willReturn($path);

        return $location;
    }

    private function mockLocationServiceLoadLocationByRemoteId(
        bool $throwNotFoundException,
        Location $location
    ): void {
        $locationServiceMock = $this->locationService
            ->expects(self::once())
            ->method('loadLocationByRemoteId')
            ->with(self::REMOTE_ID);

        if ($throwNotFoundException) {
            $locationServiceMock->willThrowException(
                $this->createMock(NotFoundException::class)
            );
        }

        $locationServiceMock->willReturn($location);
    }

    /**
     * @param array<array{
     *     string,
     *     null,
     *     string|array<string>|null
     * }> $valueMap
     */
    private function mockConfigResolverGetParameter(array $valueMap): void
    {
        $this->configResolver
            ->expects(self::atLeastOnce())
            ->method('getParameter')
            ->willReturnMap($valueMap);
    }

    private function mockRepositoryConfigurationProviderGetCurrentRepositoryAlias(): void
    {
        $this->repositoryConfigurationProvider
            ->expects(self::once())
            ->method('getCurrentRepositoryAlias')
            ->willReturn(self::REPOSITORY_ALIAS);
    }
}
