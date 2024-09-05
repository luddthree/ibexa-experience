<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\SiteContext\Menu;

use Ibexa\AdminUi\Menu\ContentRightSidebarBuilder;
use Ibexa\AdminUi\Menu\Event\ConfigureMenuEvent;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\SiteContext\SiteContextServiceInterface;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\SiteContext\Menu\ConfigureRightMenuSidebarListener;
use Knp\Menu\ItemInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @covers \Ibexa\SiteContext\Menu\ConfigureRightMenuSidebarListener
 */
final class ConfigureRightMenuSidebarListenerTest extends TestCase
{
    /**
     * @phpstan-return iterable<
     *     string,
     *     array{callable(\Ibexa\Tests\SiteContext\Menu\ConfigureRightMenuSidebarListenerTest): ?\Ibexa\Core\MVC\Symfony\SiteAccess}
     * >
     */
    public function getDataForTestOnAdminUiMenuConfigureForNonContextAwareLocations(): iterable
    {
        yield 'current context set to any SiteAccess' => [
            static fn (self $self): SiteAccess => $self->createMock(SiteAccess::class),
        ];

        yield 'current context not set' => [
            static fn (self $self): ?SiteAccess => null,
        ];
    }

    /**
     * @dataProvider getDataForTestOnAdminUiMenuConfigureForNonContextAwareLocations
     */
    public function testOnAdminUiMenuConfigureForNonContextAwareLocations(callable $buildCurrentContext): void
    {
        $siteAccessServiceMock = $this->createMock(SiteContextServiceInterface::class);
        $configResolverMock = $this->createMock(ConfigResolverInterface::class);
        $listener = new ConfigureRightMenuSidebarListener(
            $siteAccessServiceMock,
            $this->createMock(PermissionResolver::class),
            $this->createMock(UrlGeneratorInterface::class),
            $configResolverMock
        );

        $eventMock = $this->createMock(ConfigureMenuEvent::class);
        $rootMenuMock = $this->createMock(ItemInterface::class);
        $previewItemMock = $this->createMock(ItemInterface::class);
        $rootMenuMock
            ->method('getChild')
            ->with(ContentRightSidebarBuilder::ITEM__PREVIEW)
            ->willReturn($previewItemMock)
        ;
        $eventMock->method('getMenu')->willReturn($rootMenuMock);

        $siteAccessServiceMock->method('getCurrentContext')->willReturn($buildCurrentContext($this));

        $locationMock = $this->createMock(Location::class);
        $locationMock->method('__get')->with('pathString')->willReturn('/1/2/3');
        $eventMock->method('getOptions')->willReturn(
            [
                'location' => $locationMock,
                'content' => $this->createMock(Content::class),
            ]
        );
        $configResolverMock->method('getParameter')->with('site_context.excluded_paths')->willReturn(['/1/2/']);

        $previewItemMock->method('getName')->willReturn('foo');
        $rootMenuMock->expects(self::once())->method('removeChild')->with('foo');

        $previewItemMock->expects(self::never())->method('setUri');

        $listener->onAdminUiMenuConfigure($eventMock);
    }
}
