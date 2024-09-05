<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\SiteContext\Matcher;

use Ibexa\AdminUi\UserSetting\FocusMode;
use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Values\Content\Location as APILocation;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\SiteContext\SiteContextServiceInterface;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Core\MVC\Symfony\View\ContentView;
use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\Core\Repository\Values\Content\Location;
use Ibexa\SiteContext\Matcher\IsFullscreen;
use Ibexa\User\UserSetting\UserSetting;
use Ibexa\User\UserSetting\UserSettingService;
use PHPUnit\Framework\TestCase;

final class IsFullscreenTest extends TestCase
{
    /**
     * @return iterable<string, array{boolean, boolean, \Ibexa\SiteContext\Matcher\IsFullscreen, \Ibexa\Core\MVC\Symfony\View\View}>
     */
    public function getDataForTestMatch(): iterable
    {
        $view = new ContentView();
        $view->setParameters(['location' => $this->getLocation('/1/2')]);

        yield 'match when focus mode is enabled and context is provided' => [
            true,
            true,
            new IsFullscreen(
                $this->createUserSettingServiceMock('1'),
                $this->createConfigResolverMock(),
                $this->createSiteContextServiceMock(new SiteAccess('foo'))
            ),
            $view,
        ];

        yield 'do not match when focus mode is disabled' => [
            false,
            true,
            new IsFullscreen(
                $this->createUserSettingServiceMock('0'),
                $this->createConfigResolverMock(),
                $this->createSiteContextServiceMock(new SiteAccess('foo'))
            ),
            $view,
        ];

        yield 'do not match when no context is provided' => [
            false,
            true,
            new IsFullscreen(
                $this->createUserSettingServiceMock('1'),
                $this->createConfigResolverMock(),
                $this->createSiteContextServiceMock(null)
            ),
            $view,
        ];

        yield 'do not match when config is disabled' => [
            false,
            false,
            new IsFullscreen(
                $this->createUserSettingServiceMock('1'),
                $this->createConfigResolverMock(),
                $this->createSiteContextServiceMock(new SiteAccess('foo'))
            ),
            $view,
        ];

        yield 'do not match when fullscreen is disabled' => [
            false,
            true,
            new IsFullscreen(
                $this->createUserSettingServiceMock('1'),
                $this->createConfigResolverMock(),
                $this->createSiteContextServiceMock(new SiteAccess('foo'), false)
            ),
            $view,
        ];

        $view = new ContentView();
        $view->setParameters(['location' => $this->getLocation('/config/excluded')]);

        yield 'do not match when location contains excluded paths' => [
            false,
            true,
            new IsFullscreen(
                $this->createUserSettingServiceMock('1'),
                $this->createConfigResolverMock(),
                $this->createSiteContextServiceMock(new SiteAccess('foo'))
            ),
            $view,
        ];
    }

    /**
     * @dataProvider getDataForTestMatch
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function testMatch(
        bool $expectedIsFullscreen,
        bool $matching,
        IsFullscreen $matcher,
        View $view
    ): void {
        $matcher->setMatchingConfig($matching);

        self::assertSame($expectedIsFullscreen, $matcher->match($view));
    }

    public function testSetMatchConfigThrowsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Ibexa\SiteContext\Matcher\IsFullscreen matcher expects boolean value, got a value of int type'
        );

        $isFullscreen = new IsFullscreen(
            $this->createUserSettingServiceMock('1'),
            $this->createConfigResolverMock(),
            $this->createSiteContextServiceMock(new SiteAccess('foo'))
        );

        $isFullscreen->setMatchingConfig(123);
    }

    private function createUserSettingServiceMock(string $isFocusModeEnabled): UserSettingService
    {
        $userSetting = new UserSetting(['value' => $isFocusModeEnabled]);

        $mock = $this->createMock(UserSettingService::class);
        $mock
            ->method('getUserSetting')
            ->with(FocusMode::IDENTIFIER)
            ->willReturn($userSetting);

        return $mock;
    }

    private function createConfigResolverMock(): ConfigResolverInterface
    {
        $mock = $this->createMock(ConfigResolverInterface::class);
        $mock
            ->method('getParameter')
            ->with('site_context.excluded_paths')
            ->willReturn(['/config/excluded']);

        return $mock;
    }

    private function createSiteContextServiceMock(
        ?SiteAccess $context,
        bool $isFullscreenModeEnabled = true
    ): SiteContextServiceInterface {
        $mock = $this->createMock(SiteContextServiceInterface::class);
        $mock->method('getCurrentContext')->willReturn($context);
        $mock->method('isFullscreenModeEnabled')->willReturn($isFullscreenModeEnabled);

        return $mock;
    }

    private function getLocation(string $pathString): APILocation
    {
        return new Location([
            'pathString' => $pathString,
        ]);
    }
}
