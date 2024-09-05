<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Dashboard\SiteAccess\Resolver\Strategy;

use Ibexa\Contracts\Core\Persistence\Content\Location as PersistenceLocation;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Core\Repository\Values\Content\Location;
use Ibexa\Dashboard\SiteAccess\Resolver\Strategy\DashboardPageBuilderSiteAccessListResolverStrategy;
use Ibexa\Tests\Dashboard\PhpUnit\ConfigResolverMockTrait;
use Ibexa\Tests\Dashboard\PhpUnit\ContentItemOfContentTypeMockTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Dashboard\SiteAccess\Resolver\Strategy\DashboardPageBuilderSiteAccessListResolverStrategy
 */
final class DashboardPageBuilderSiteAccessListResolverStrategyTest extends TestCase
{
    use ConfigResolverMockTrait;
    use ContentItemOfContentTypeMockTrait;

    private DashboardPageBuilderSiteAccessListResolverStrategy $strategy;

    protected function setUp(): void
    {
        $siteAccessServiceMock = $this->createMock(SiteAccessServiceInterface::class);
        $siteAccessServiceMock->method('getCurrent')->willReturn(new SiteAccess('admin_en'));

        $dashboardContainerLocation = $this->getPersistenceLocation('/1/56/');
        $locationHandler = $this->createMock(PersistenceLocation\Handler::class);
        $locationHandler
            ->method('loadByRemoteId')
            ->willReturn($dashboardContainerLocation);

        $this->strategy = new DashboardPageBuilderSiteAccessListResolverStrategy(
            $siteAccessServiceMock,
            $this->mockConfigResolverWithMap(
                [
                    ['dashboard.content_type_identifier', null, null, 'dashboard_landing_page'],
                    ['dashboard.container_remote_id', null, null, 'dashboard_container'],
                ]
            ),
            $locationHandler,
            [
                'site' => ['site_pl', 'site_en'],
                'admin_group' => ['admin_pl', 'admin_en'],
            ]
        );
    }

    /**
     * @return iterable<string, array{\Ibexa\Contracts\Core\Repository\Values\Content\Content, \Ibexa\Core\MVC\Symfony\SiteAccess[]|null}>
     */
    public function getDataForTestGetSiteAccessListForContent(): iterable
    {
        yield 'dashboard content' => [
            $this->mockContentItemOfDashboardType(),
            [new SiteAccess('admin_en')],
        ];

        yield 'non-dashboard content' => [
            $this->mockContentItemOfContentType('folder'),
            null,
        ];
    }

    /**
     * @return iterable<string, array{\Ibexa\Contracts\Core\Repository\Values\Content\Location, \Ibexa\Core\MVC\Symfony\SiteAccess[]|null}>
     */
    public function getDataForTestGetSiteAccessListForLocation(): iterable
    {
        yield 'dashboard Location' => [
            $this->mockLocationOfContentItemOfDashboardType(),
            [new SiteAccess('admin_en')],
        ];

        yield 'non-dashboard Location' => [
            $this->mockLocationOfContentItemOfContentType('folder'),
            null,
        ];
    }

    /**
     * @dataProvider getDataForTestGetSiteAccessListForContent
     *
     * @param \Ibexa\Core\MVC\Symfony\SiteAccess[]|null $expectedList
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function testGetSiteAccessListForContent(Content $content, ?array $expectedList): void
    {
        $actualList = $this->strategy->getSiteAccessListForContent($content);
        self::assertEquals($expectedList, $actualList);
    }

    /**
     * @dataProvider getDataForTestGetSiteAccessListForLocation
     *
     * @param \Ibexa\Core\MVC\Symfony\SiteAccess[]|null $expectedList
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function testGetSiteAccessListForLocation(Location $location, ?array $expectedList): void
    {
        $actualList = $this->strategy->getSiteAccessListForLocation($location, null, null);
        self::assertEquals($expectedList, $actualList);
    }

    public function testGetSiteAccessList(): void
    {
        // null indicates that another strategy should be used; the Dashboard needs a context to be able to return a list
        self::assertNull($this->strategy->getSiteAccessList());
    }

    private function getPersistenceLocation(string $pathString): PersistenceLocation
    {
        return new PersistenceLocation(['pathString' => $pathString]);
    }
}
