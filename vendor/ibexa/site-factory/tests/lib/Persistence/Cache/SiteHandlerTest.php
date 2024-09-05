<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\EzPlatformSiteFactory\Tests\Persistence\Cache;

use eZ\Publish\Core\Persistence\Cache\PersistenceLogger;
use EzSystems\EzPlatformSiteFactory\Persistence\Cache\SiteHandler;
use EzSystems\EzPlatformSiteFactory\Persistence\Site\Gateway\AbstractGateway;
use EzSystems\EzPlatformSiteFactory\Persistence\Site\Handler\SiteHandler as DecoratedSiteHandler;
use EzSystems\EzPlatformSiteFactory\SiteDomainMapper;
use EzSystems\EzPlatformSiteFactory\Values\Site\PublicAccess;
use EzSystems\EzPlatformSiteFactory\Values\Site\SiteAccessMatcherConfiguration;
use EzSystems\EzPlatformSiteFactory\Values\Site\SiteCreateStruct;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

final class SiteHandlerTest extends TestCase
{
    /** @var \eZ\Publish\Core\Persistence\Cache\PersistenceLogger|\PHPUnit\Framework\MockObject\MockObject */
    private $logger;

    /** @var \Symfony\Contracts\Cache\TagAwareCacheInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $cache;

    /** @var \EzSystems\EzPlatformSiteFactory\Persistence\Site\Gateway\AbstractGateway|\PHPUnit\Framework\MockObject\MockObject */
    private $gateway;

    /** @var \EzSystems\EzPlatformSiteFactory\SiteDomainMapper|\PHPUnit\Framework\MockObject\MockObject */
    private $mapper;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(PersistenceLogger::class);
        $this->cache = $this->createMock(TagAwareCacheInterface::class);
        $this->gateway = $this->createMock(AbstractGateway::class);
        $this->mapper = $this->createMock(SiteDomainMapper::class);
    }

    public function testWillInvalidateCacheTagsOnSiteCreation()
    {
        $publicAccess1 = new PublicAccess(
            'site1',
            1,
            'group1',
            new SiteAccessMatcherConfiguration('ibexa.co', 'site1')
        );

        $publicAccess2 = new PublicAccess(
            'site2',
            2,
            'group2',
            new SiteAccessMatcherConfiguration('ibexa.co', 'site2')
        );

        $siteCreateStruct = new SiteCreateStruct(
            'site1',
            true,
            [$publicAccess1, $publicAccess2],
            100,
            [200]
        );

        $this->cache->expects($this->once())
            ->method('invalidateTags')
            ->with(['ez-public-accesses-ibexa.co', 'ez-public-accesses-ibexa.co']);

        $siteHandler = new SiteHandler(
            $this->cache,
            $this->logger,
            new DecoratedSiteHandler(
                $this->gateway,
                $this->mapper,
                'a_string'
            )
        );

        $siteHandler->create($siteCreateStruct);
    }
}
