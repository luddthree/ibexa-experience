<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\SiteContext\EventSubscriber;

use Ibexa\Bundle\AdminUi\IbexaAdminUiBundle;
use Ibexa\Bundle\SiteContext\EventSubscriber\LocationFilterSubscriber;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\SiteContext\SiteContextServiceInterface;
use Ibexa\Core\MVC\Symfony\Event\PreContentViewEvent;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Core\MVC\Symfony\SiteAccessGroup;
use Ibexa\Core\MVC\Symfony\View\ContentView;
use Ibexa\Core\MVC\Symfony\View\View;
use PHPUnit\Framework\TestCase;

final class LocationFilterSubscriberTest extends TestCase
{
    private const EXAMPLE_ROOT_LOCATION_ID = 43;

    /** @var \Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface&\PHPUnit\Framework\MockObject\MockObject */
    private SiteAccessServiceInterface $siteAccessService;

    /** @var \Ibexa\Contracts\SiteContext\SiteContextServiceInterface&\PHPUnit\Framework\MockObject\MockObject */
    private SiteContextServiceInterface $siteContextService;

    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface&\PHPUnit\Framework\MockObject\MockObject */
    private ConfigResolverInterface $configResolver;

    private LocationFilterSubscriber $subscriber;

    protected function setUp(): void
    {
        $this->siteAccessService = $this->createMock(SiteAccessServiceInterface::class);
        $this->siteContextService = $this->createMock(SiteContextServiceInterface::class);
        $this->configResolver = $this->createMock(ConfigResolverInterface::class);

        $this->subscriber = new LocationFilterSubscriber(
            $this->siteAccessService,
            $this->siteContextService,
            $this->configResolver
        );
    }

    public function testOnPreContentViewDoesNothingForNonContentView(): void
    {
        $view = $this->createMock(View::class);

        $this->expectViewHasNotBeenModified($view);

        $this->subscriber->onPreContentView(new PreContentViewEvent($view));
    }

    public function testOnPreContentViewDoesNothingForNonAdminSiteAccess(): void
    {
        $view = $this->createMock(ContentView::class);

        $this->siteAccessService
            ->method('getCurrent')
            ->willReturn($this->createNonAdminSiteAccess());

        $this->expectViewHasNotBeenModified($view);

        $this->subscriber->onPreContentView(new PreContentViewEvent($view));
    }

    public function testOnPreContentViewDoesNothingForNonContextAwareLocation(): void
    {
        $location = $this->createMock(Location::class);
        $location->method('__get')->with('pathString')->willReturn('/1/2/');

        $view = $this->createMock(ContentView::class);

        $this->assumeIsCurrentSiteAccessAdmin();
        $this->assumeLocationIsNotContextAware($location);

        $this->expectViewHasNotBeenModified($view);

        $this->subscriber->onPreContentView(new PreContentViewEvent($view));
    }

    public function testOnPreContentViewAppliesLocationFilter(): void
    {
        $location = $this->createMock(Location::class);

        $view = new ContentView();
        $view->setLocation($location);
        $view->addParameters([
            'path_locations' => $this->createLocationPath([1, 2, 43, 54]),
        ]);

        $siteAccess = $this->createNonAdminSiteAccess();

        $this->assumeIsCurrentSiteAccessAdmin();
        $this->assumeSiteAccessIsCurrentContext($siteAccess);
        $this->assumeLocationIsContextAwareAndTreeRootLocationForSiteAccessIsDefined($location, $siteAccess);

        $this->subscriber->onPreContentView(new PreContentViewEvent($view));

        self::assertTrue($view->hasParameter('content_tree_module_root'));
        self::assertEquals(self::EXAMPLE_ROOT_LOCATION_ID, $view->getParameter('content_tree_module_root'));
        self::assertTrue($view->hasParameter('path_locations'));
        self::assertEquals($this->createLocationPath([43, 54]), $view->getParameter('path_locations'));
    }

    /**
     * @param \Ibexa\Core\MVC\Symfony\View\View&\PHPUnit\Framework\MockObject\MockObject $view
     */
    private function expectViewHasNotBeenModified(View $view): void
    {
        $view->expects($this->never())->method('addParameters')->withAnyParameters();
        $view->expects($this->never())->method('setParameters')->withAnyParameters();
    }

    /**
     * @param int[] $ids
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Location[]
     */
    private function createLocationPath(array $ids): array
    {
        $path = [];
        foreach ($ids as $id) {
            $location = $this->createMock(Location::class);
            $location->method('__get')->with('id')->willReturn($id);

            $path[] = $location;
        }

        return $path;
    }

    private function assumeLocationIsNotContextAware(Location $location): void
    {
        $this->configResolver
            ->method('getParameter')
            ->with('site_context.excluded_paths')
            ->willReturn(['/']);
    }

    private function assumeLocationIsContextAwareAndTreeRootLocationForSiteAccessIsDefined(
        Location $location,
        SiteAccess $siteAccess
    ): void {
        $this->configResolver
            ->method('getParameter')
            ->willReturnMap([
                [
                    'content.tree_root.location_id',
                    null,
                    $siteAccess->name,
                    self::EXAMPLE_ROOT_LOCATION_ID,
                ],
                ['site_context.excluded_paths', null, null, null],
            ]);
    }

    private function assumeIsCurrentSiteAccessAdmin(): void
    {
        $this->siteAccessService->method('getCurrent')->willReturn($this->createAdminSiteAccess());
    }

    private function assumeSiteAccessIsCurrentContext(SiteAccess $siteAccess): void
    {
        $this->siteContextService->method('getCurrentContext')->willReturn($siteAccess);
    }

    private function createNonAdminSiteAccess(): SiteAccess
    {
        return $this->createSiteAccessWithGroup('site', 'site_group');
    }

    private function createAdminSiteAccess(): SiteAccess
    {
        return $this->createSiteAccessWithGroup('admin', IbexaAdminUiBundle::ADMIN_GROUP_NAME);
    }

    private function createSiteAccessWithGroup(string $name, string $groupName): SiteAccess
    {
        $siteAccess = $this->createMock(SiteAccess::class);
        $siteAccess->name = $name;
        $siteAccess->groups = [
            new SiteAccessGroup($groupName),
        ];

        return $siteAccess;
    }
}
