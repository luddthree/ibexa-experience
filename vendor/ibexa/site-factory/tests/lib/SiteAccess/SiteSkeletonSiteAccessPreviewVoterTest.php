<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\SiteFactory\SiteAccess;

use Ibexa\AdminUi\Siteaccess\SiteaccessPreviewVoterContext;
use Ibexa\Bundle\Core\ApiLoader\RepositoryConfigurationProvider;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\SiteFactory\SiteAccess\SiteSkeletonSiteAccessPreviewVoter;
use PHPUnit\Framework\TestCase;

final class SiteSkeletonSiteAccessPreviewVoterTest extends TestCase
{
    private const EXAMPLE_SKELETONS_LOCATION_ID = 56;
    private const EXAMPLE_SITEACCESS_NAME = 'site_fr';

    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $configResolver;

    /** @var \Ibexa\Bundle\Core\ApiLoader\RepositoryConfigurationProvider */
    private $repositoryConfigurationProvider;

    /** @var \Ibexa\SiteFactory\SiteAccess\SiteSkeletonSiteAccessPreviewVoter */
    private $siteAccessPreviewVoter;

    protected function setUp(): void
    {
        $this->configResolver = $this->createMock(ConfigResolverInterface::class);

        $this->repositoryConfigurationProvider = $this->createMock(RepositoryConfigurationProvider::class);
        $this->repositoryConfigurationProvider->method('getDefaultRepositoryAlias')->willReturn('default');
        $this->repositoryConfigurationProvider->method('getCurrentRepositoryAlias')->willReturn('current');

        $this->siteAccessPreviewVoter = new SiteSkeletonSiteAccessPreviewVoter(
            $this->configResolver,
            $this->repositoryConfigurationProvider
        );
    }

    /**
     * @dataProvider dataProviderForTestVote
     */
    public function testVote(
        SiteaccessPreviewVoterContext $context,
        int $siteSkeletonsLocationId,
        ?string $reposiotry,
        array $languages,
        bool $expectedResult
    ): void {
        $this->configResolver
            ->method('getParameter')
            ->willReturnMap([
                [
                    'site_factory.site_skeletons_location_id',
                    null,
                    self::EXAMPLE_SITEACCESS_NAME,
                    $siteSkeletonsLocationId,
                ],
                [
                    'repository',
                    null,
                    self::EXAMPLE_SITEACCESS_NAME,
                    $reposiotry,
                ],
                [
                    'languages',
                    null,
                    self::EXAMPLE_SITEACCESS_NAME,
                    $languages,
                ],
            ]);

        $this->assertEquals(
            $expectedResult,
            $this->siteAccessPreviewVoter->vote($context)
        );
    }

    public function dataProviderForTestVote(): iterable
    {
        yield 'happy path' => [
            new SiteaccessPreviewVoterContext(
                $this->createLocationWithPath([1, 2, 56, 130]),
                $this->createVersionInfoWithLanguageCodes(['fre-FR']),
                self::EXAMPLE_SITEACCESS_NAME,
                'fre-FR'
            ),
            self::EXAMPLE_SKELETONS_LOCATION_ID,
            'current',
            ['fre-FR', 'eng-GB'],
            true,
        ];

        yield 'out of the tree root' => [
            new SiteaccessPreviewVoterContext(
                $this->createLocationWithPath([1, 2, 43]),
                $this->createVersionInfoWithLanguageCodes(['fre-FR']),
                self::EXAMPLE_SITEACCESS_NAME,
                'fre-FR'
            ),
            self::EXAMPLE_SKELETONS_LOCATION_ID,
            'current',
            ['fre-FR', 'eng-GB'],
            false,
        ];

        yield 'invalid repository' => [
            new SiteaccessPreviewVoterContext(
                $this->createLocationWithPath([1, 2, 43]),
                $this->createVersionInfoWithLanguageCodes(['fre-FR']),
                self::EXAMPLE_SITEACCESS_NAME,
                'fre-FR'
            ),
            self::EXAMPLE_SKELETONS_LOCATION_ID,
            'custom',
            ['fre-FR', 'eng-GB'],
            false,
        ];

        yield 'not supported language' => [
            new SiteaccessPreviewVoterContext(
                $this->createLocationWithPath([1, 2, 43]),
                $this->createVersionInfoWithLanguageCodes(['fre-FR']),
                self::EXAMPLE_SITEACCESS_NAME,
                'fre-FR'
            ),
            self::EXAMPLE_SKELETONS_LOCATION_ID,
            'current',
            ['eng-GB'],
            false,
        ];
    }

    private function createLocationWithPath(array $locationPath): Location
    {
        $location = $this->createMock(Location::class);
        $location->method('getPath')->willReturn($locationPath);

        return $location;
    }

    private function createVersionInfoWithLanguageCodes(array $languageCodes): VersionInfo
    {
        $versionInfo = $this->createMock(VersionInfo::class);
        $versionInfo->method('__get')->with('languageCodes')->willReturn($languageCodes);

        return $versionInfo;
    }
}

class_alias(SiteSkeletonSiteAccessPreviewVoterTest::class, 'EzSystems\EzPlatformSiteFactory\Tests\SiteAccess\SiteSkeletonSiteAccessPreviewVoterTest');
