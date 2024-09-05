<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\PageBuilder\Siteaccess\Resolver;

use Ibexa\AdminUi\Siteaccess\SiteaccessResolverInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\PageBuilder\PageBuilder\ConfigurationResolverInterface;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\PageBuilder\Siteaccess\Resolver\PageBuilderListResolverStrategy;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\PageBuilder\Siteaccess\Resolver\PageBuilderListResolverStrategy
 */
final class PageBuilderListResolverStrategyTest extends TestCase
{
    private PageBuilderListResolverStrategy $listResolverStrategy;

    private SiteAccess $siteSiteAccess;

    private SiteAccess $anotherSiteSiteAccess;

    /** @var \Ibexa\AdminUi\Siteaccess\SiteaccessResolverInterface&\PHPUnit\Framework\MockObject\MockObject */
    private SiteaccessResolverInterface $nonAdminSiteAccessResolverMock;

    protected function setUp(): void
    {
        $this->siteSiteAccess = new SiteAccess('site');
        $this->anotherSiteSiteAccess = new SiteAccess('site2');

        $configurationMock = $this->createMock(ConfigurationResolverInterface::class);
        $configurationMock->method('getSiteaccessList')->willReturn(['site']);

        $siteAccessServiceMock = $this->createMock(SiteAccessServiceInterface::class);
        $siteAccessServiceMock->method('get')->with('site')->willReturn($this->siteSiteAccess);

        $this->nonAdminSiteAccessResolverMock = $this->createMock(SiteaccessResolverInterface::class);

        $this->listResolverStrategy = new PageBuilderListResolverStrategy(
            $this->nonAdminSiteAccessResolverMock,
            $configurationMock,
            $siteAccessServiceMock,
        );
    }

    public function testGetSiteAccessListForContent(): void
    {
        $contentMock = $this->createMock(Content::class);
        $this->nonAdminSiteAccessResolverMock
            ->method('getSiteAccessesListForContent')
            ->with($contentMock)
            ->willReturn(
                [
                    $this->anotherSiteSiteAccess,
                    $this->siteSiteAccess,
                ]
            )
        ;

        self::assertSame(
            [$this->siteSiteAccess],
            $this->listResolverStrategy->getSiteAccessListForContent($contentMock)
        );
    }

    public function testGetSiteAccessListForLocation(): void
    {
        $locationMock = $this->createMock(Location::class);
        $this->nonAdminSiteAccessResolverMock
            ->method('getSiteAccessesListForLocation')
            ->with($locationMock)
            ->willReturn(
                [
                    $this->anotherSiteSiteAccess,
                    $this->siteSiteAccess,
                ]
            )
        ;

        self::assertSame(
            [$this->siteSiteAccess],
            $this->listResolverStrategy->getSiteAccessListForLocation($locationMock, null, null)
        );
    }

    public function testGetSiteAccessList(): void
    {
        self::assertSame([$this->siteSiteAccess], $this->listResolverStrategy->getSiteAccessList());
    }
}
