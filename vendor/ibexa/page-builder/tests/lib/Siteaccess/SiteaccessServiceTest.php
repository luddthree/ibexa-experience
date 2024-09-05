<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\PageBuilder\Siteaccess;

use Ibexa\AdminUi\Siteaccess\NonAdminSiteaccessResolver;
use Ibexa\AdminUi\Siteaccess\SiteaccessResolverInterface;
use Ibexa\Bundle\PageBuilder\DependencyInjection\IbexaPageBuilderExtension;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\Location;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use Ibexa\PageBuilder\PageBuilder\PermissionAwareConfigurationResolver;
use Ibexa\PageBuilder\Siteaccess\SiteaccessService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class SiteaccessServiceTest extends TestCase
{
    /** @var \Ibexa\PageBuilder\Siteaccess\SiteaccessService */
    private $siteaccessService;

    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $configResolver;

    /** @var \Ibexa\Contracts\Core\Repository\LocationService|\PHPUnit\Framework\MockObject\MockObject */
    private $locationService;

    /** @var \Ibexa\AdminUi\Siteaccess\NonAdminSiteaccessResolver|\PHPUnit\Framework\MockObject\MockObject */
    private $nonAdminSiteaccessResolver;

    /** @var \Ibexa\PageBuilder\PageBuilder\PermissionAwareConfigurationResolver|\PHPUnit\Framework\MockObject\MockObject */
    private $pageBuilderPermissionAwareConfigurationResolver;

    /** @var \Symfony\Component\HttpFoundation\Session\SessionInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $session;

    /** @var \Ibexa\AdminUi\Siteaccess\SiteaccessResolverInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $siteaccessResolver;

    public function setUp(): void
    {
        $this->configResolver = $this->createMock(ConfigResolverInterface::class);
        $this->locationService = $this->createMock(LocationService::class);
        $this->nonAdminSiteaccessResolver = $this->createMock(NonAdminSiteaccessResolver::class);
        $this->pageBuilderPermissionAwareConfigurationResolver = $this->createMock(PermissionAwareConfigurationResolver::class);
        $this->session = $this->createMock(SessionInterface::class);
        $this->siteaccessResolver = $this->createMock(SiteaccessResolverInterface::class);

        $this->siteaccessService = new SiteaccessService(
            $this->configResolver,
            $this->locationService,
            $this->nonAdminSiteaccessResolver,
            $this->pageBuilderPermissionAwareConfigurationResolver,
            $this->session,
            $this->siteaccessResolver,
        );
    }

    /**
     * @dataProvider providerForResolveSiteAccessForLocation
     */
    public function testResolveSiteAccessForLocationWithRequestSiteAccess(
        string $requestSiteAccess,
        string $sessionSiteAccess,
        array $configuredSiteAccesses,
        Language $language
    ): void {
        $location = new Location([
            'id' => 234,
            'parentLocationId' => 2,
            'pathString' => '/1/2/234/',
        ]);

        $this->session
            ->expects(self::atLeastOnce())
            ->method('get')
            ->with(IbexaPageBuilderExtension::SESSION_KEY_SITEACCESS)
            ->willReturn($sessionSiteAccess);

        $configuredSiteAccesses = array_map(
            static fn (string $siteAccessName): SiteAccess => new SiteAccess($siteAccessName),
            $configuredSiteAccesses
        );

        $this->nonAdminSiteaccessResolver
            ->expects(self::once())
            ->method('getSiteAccessesListForLocation')
            ->with($location, null, $language->languageCode)
            ->willReturn($configuredSiteAccesses);

        $resolvedSiteaccess = $this->siteaccessService->resolveSiteAccessForLocation(
            $language,
            $location,
            $requestSiteAccess
        )[0];

        self::assertEquals($requestSiteAccess, $resolvedSiteaccess);
    }

    /**
     * @dataProvider providerForResolveSiteAccessForLocation
     */
    public function testResolveSiteAccessForLocationWithEmptyLocation(
        string $requestSiteAccess,
        string $sessionSiteAccess,
        array $configuredSiteAccesses,
        Language $language
    ): void {
        $location = null;

        $this->session
            ->expects(self::atLeastOnce())
            ->method('get')
            ->with(IbexaPageBuilderExtension::SESSION_KEY_SITEACCESS)
            ->willReturn($sessionSiteAccess);

        $this->pageBuilderPermissionAwareConfigurationResolver
            ->expects(self::atLeastOnce())
            ->method('getSiteaccessList')
            ->willReturn($configuredSiteAccesses);

        $resolvedSiteaccess = $this->siteaccessService->resolveSiteAccessForLocation(
            $language,
            $location,
            $requestSiteAccess
        )[0];

        self::assertEquals($requestSiteAccess, $resolvedSiteaccess);
    }

    public function providerForResolveSiteAccessForLocation(): iterable
    {
        $requestSiteAccess = 'site';
        $sessionSiteAccess = 'session';
        $configuredSiteAccesses = [$requestSiteAccess, $sessionSiteAccess, 'ger'];
        $language = new Language(['languageCode' => 'eng-GB']);

        yield [$requestSiteAccess, $sessionSiteAccess, $configuredSiteAccesses, $language];
    }

    public function testResolveSiteAccessForContent(): void
    {
        $germanLanguageCode = 'ger-DE';
        $englishLanguageCode = 'eng-GB';

        $versionInfo = new VersionInfo([
            'contentInfo' => new ContentInfo([
                'mainLanguageCode' => $englishLanguageCode,
                'alwaysAvailable' => false,
                'mainLocationId' => 123,
            ]),
            'languages' => [
                new Language(['languageCode' => $germanLanguageCode]),
                new Language(['languageCode' => $englishLanguageCode]),
            ],
        ]);
        $location = new Location([
            'id' => 123,
            'parentLocationId' => 2,
            'pathString' => '/1/2/123/',
        ]);
        $content = new Content(['versionInfo' => $versionInfo]);

        $fallback = 'site';
        $properSiteAccess = 'site_de';
        $configuredSiteAccesses = ['site', 'site_de'];

        $this->locationService
            ->expects(self::once())
            ->method('loadLocations')
            ->with($content->contentInfo)
            ->willReturn([$location]);

        $this->nonAdminSiteaccessResolver
            ->expects(self::atLeastOnce())
            ->method('getSiteaccessesForLocation')
            ->with($location, null, $germanLanguageCode)
            ->willReturn([$properSiteAccess]);

        $this->pageBuilderPermissionAwareConfigurationResolver
            ->expects(self::atLeastOnce())
            ->method('getSiteaccessList')
            ->willReturn($configuredSiteAccesses);

        $this->configResolver
            ->expects(self::once())
            ->method('getParameter')
            ->with('languages', null, $properSiteAccess)
            ->willReturn([$germanLanguageCode]);

        $resolvedSiteaccess = $this->siteaccessService->resolveSiteAccessForContent($content, $fallback);

        self::assertEquals($properSiteAccess, $resolvedSiteaccess);
    }

    public function testResolveSiteAccessForContentWithLanguageProvided(): void
    {
        $germanLanguageCode = 'ger-DE';
        $englishLanguageCode = 'eng-GB';

        $versionInfo = new VersionInfo([
            'contentInfo' => new ContentInfo([
                'mainLanguageCode' => $englishLanguageCode,
                'alwaysAvailable' => false,
                'mainLocationId' => 123,
            ]),
            'languages' => [
                new Language(['languageCode' => $germanLanguageCode]),
                new Language(['languageCode' => $englishLanguageCode]),
            ],
        ]);
        $location = new Location([
            'id' => 123,
            'parentLocationId' => 2,
            'pathString' => '/1/2/123/',
        ]);
        $content = new Content(['versionInfo' => $versionInfo]);

        $englishSiteAccess = 'site';
        $germanSiteAccess = 'site_de';
        $configuredSiteAccesses = [$englishSiteAccess, $germanSiteAccess];

        $this->locationService
            ->expects(self::once())
            ->method('loadLocations')
            ->with($content->contentInfo)
            ->willReturn([$location]);

        $this->nonAdminSiteaccessResolver
            ->expects(self::atLeastOnce())
            ->method('getSiteaccessesForLocation')
            ->with($location, null, $germanLanguageCode)
            ->willReturn([$germanSiteAccess]);

        $this->pageBuilderPermissionAwareConfigurationResolver
            ->expects(self::atLeastOnce())
            ->method('getSiteaccessList')
            ->willReturn($configuredSiteAccesses);

        $this->session
            ->expects(self::atLeastOnce())
            ->method('get')
            ->with(IbexaPageBuilderExtension::SESSION_KEY_SITEACCESS)
            ->willReturn($englishSiteAccess);

        $this->configResolver
            ->expects(self::once())
            ->method('getParameter')
            ->with('languages', null, $germanSiteAccess)
            ->willReturn([$germanLanguageCode, $englishLanguageCode]);

        $resolvedSiteaccess = $this->siteaccessService->resolveSiteAccessForContent(
            $content,
            'site',
            $germanLanguageCode
        );

        self::assertEquals($germanSiteAccess, $resolvedSiteaccess);
    }
}
