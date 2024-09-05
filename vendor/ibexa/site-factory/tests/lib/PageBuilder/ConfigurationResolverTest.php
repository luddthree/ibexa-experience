<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\SiteFactory\PageBuilder;

use Ibexa\Contracts\PageBuilder\PageBuilder\ConfigurationResolverInterface;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\SiteFactory\PageBuilder\ConfigurationResolver;
use Ibexa\SiteFactory\SiteAccessProvider;
use PHPUnit\Framework\TestCase;

final class ConfigurationResolverTest extends TestCase
{
    /** @var \Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface&\PHPUnit\Framework\MockObject\MockObject */
    private SiteAccessServiceInterface $siteAccessService;

    /** @var \Ibexa\Contracts\PageBuilder\PageBuilder\ConfigurationResolverInterface&\PHPUnit\Framework\MockObject\MockObject */
    private ConfigurationResolverInterface $inner;

    /** @var \Ibexa\SiteFactory\PageBuilder\ConfigurationResolver */
    private ConfigurationResolver $configurationResolver;

    protected function setUp(): void
    {
        $this->siteAccessService = $this->createMock(SiteAccessServiceInterface::class);
        $this->inner = $this->createMock(ConfigurationResolverInterface::class);

        $this->configurationResolver = new ConfigurationResolver(
            $this->inner,
            $this->siteAccessService
        );
    }

    /**
     * @covers \Ibexa\SiteFactory\PageBuilder\ConfigurationResolver::GetSiteaccessList
     */
    public function testGetSiteaccessList(): void
    {
        $this->inner
            ->expects(self::once())
            ->method('getSiteaccessList')
            ->willReturn(['site']);

        $this->siteAccessService
            ->expects(self::once())
            ->method('getAll')
            ->willReturn([
                new SiteAccess(
                    'factory',
                    SiteAccess::DEFAULT_MATCHING_TYPE,
                    null,
                    SiteAccessProvider::class
                ),
            ]);

        $expectedResult = ['site', 'factory'];

        self::assertSame($expectedResult, $this->configurationResolver->getSiteaccessList());
    }
}
