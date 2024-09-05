<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\SiteFactory\Pagination\Pagerfanta;

use ArrayIterator;
use Ibexa\Contracts\SiteFactory\Service\SiteServiceInterface;
use Ibexa\Contracts\SiteFactory\Values\Site\Site;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteList;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteQuery;
use Ibexa\SiteFactory\Pagination\Pagerfanta\SiteAdapter;
use PHPUnit\Framework\TestCase;

final class SiteAdapterTest extends TestCase
{
    /** @var \Ibexa\Contracts\SiteFactory\Service\SiteServiceInterface */
    private $siteService;

    protected function setUp(): void
    {
        $this->siteService = $this->createMock(SiteServiceInterface::class);
    }

    public function testGetNbResults(): void
    {
        $this->siteService
            ->expects($this->once())
            ->method('countSites')
            ->willReturn(10);
        $query = new SiteQuery();
        $siteAdapter = new SiteAdapter($this->siteService, $query);

        $this->assertSame(10, $siteAdapter->getNbResults());
        // check if SiteServiceInterface::countSites is called only once
        $this->assertSame(10, $siteAdapter->getNbResults());
    }

    public function testGetSlice(): void
    {
        $site1 = new Site(['id' => 1]);
        $site2 = new Site(['id' => 2]);
        $page = 'site';

        $siteList = new SiteList(
            5,
            [$site1, $site2],
            [$page]
        );
        $query = new SiteQuery();
        $query->limit = 3;

        $this->siteService
            ->expects($this->once())
            ->method('loadSites')
            ->with($query)
            ->willReturn($siteList);

        $siteAdapter = new SiteAdapter($this->siteService, new SiteQuery());

        $this->assertEquals(
            new ArrayIterator([
                $site1,
                $site2,
                $page,
            ]),
            $siteAdapter->getSlice(0, 3)
        );
    }
}

class_alias(SiteAdapterTest::class, 'EzSystems\EzPlatformSiteFactory\Tests\Pagination\Pagerfanta\SiteAdapterTest');
