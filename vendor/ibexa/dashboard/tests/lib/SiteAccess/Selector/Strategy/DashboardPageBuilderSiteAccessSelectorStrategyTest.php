<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Dashboard\SiteAccess\Selector\Strategy;

use Ibexa\Contracts\PageBuilder\Siteaccess\Selector\Context;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Dashboard\SiteAccess\Selector\Strategy\DashboardPageBuilderSiteAccessSelectorStrategy;
use Ibexa\Tests\Dashboard\PhpUnit\ConfigResolverMockTrait;
use Ibexa\Tests\Dashboard\PhpUnit\ContentItemOfContentTypeMockTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Dashboard\SiteAccess\Selector\Strategy\DashboardPageBuilderSiteAccessSelectorStrategy
 */
final class DashboardPageBuilderSiteAccessSelectorStrategyTest extends TestCase
{
    use ContentItemOfContentTypeMockTrait;
    use ConfigResolverMockTrait;

    private DashboardPageBuilderSiteAccessSelectorStrategy $selectorStrategy;

    /** @var \Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface&\PHPUnit\Framework\MockObject\MockObject */
    private SiteAccessServiceInterface $siteAccessServiceMock;

    protected function setUp(): void
    {
        $this->siteAccessServiceMock = $this->createMock(SiteAccessServiceInterface::class);
        $this->selectorStrategy = new DashboardPageBuilderSiteAccessSelectorStrategy(
            $this->siteAccessServiceMock,
            $this->mockConfigResolver(),
            [
                'site_group' => ['site-pl', 'site-en'],
                'admin_group' => ['admin-pl', 'admin-en'],
            ]
        );
    }

    /**
     * @return iterable<string, array{\Ibexa\Contracts\PageBuilder\Siteaccess\Selector\Context, string[], string, ?string}>
     */
    public function getDataForTestSelectSiteAccess(): iterable
    {
        yield 'under admin-en SA for Dashboard content item' => [
            new Context(
                null,
                $this->mockContentItemOfDashboardType()
            ),
            ['site-en', 'admin-en'],
            'admin-en',
            'admin-en',
        ];

        yield 'under admin-en SA for other type of content item' => [
            new Context(null, $this->mockContentItemOfContentType('foo')),
            ['site-en', 'admin-en'],
            'admin-en',
            null,
        ];

        yield 'under site-pl SA for Dashboard content item' => [
            new Context(
                null,
                $this->mockContentItemOfDashboardType()
            ),
            ['site-en', 'admin-en'],
            'site-pl',
            null,
        ];
    }

    /**
     * @dataProvider getDataForTestSelectSiteAccess
     *
     * @param string[] $siteAccessNameList
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function testSelectSiteAccess(
        Context $context,
        array $siteAccessNameList,
        string $currentSiteAccessName,
        ?string $expectedSelectedSiteAccessName
    ): void {
        $this
            ->siteAccessServiceMock
            ->method('getCurrent')
            ->willReturn(new SiteAccess($currentSiteAccessName))
        ;

        self::assertSame(
            $expectedSelectedSiteAccessName,
            $this->selectorStrategy->selectSiteAccess($context, $siteAccessNameList)
        );
    }
}
