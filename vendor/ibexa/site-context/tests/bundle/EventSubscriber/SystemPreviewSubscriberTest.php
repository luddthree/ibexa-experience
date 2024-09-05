<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\SiteContext\EventSubscriber;

use Ibexa\Bundle\SiteContext\EventSubscriber\SystemPreviewSubscriber;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\SiteContext\Event\ResolveLocationPreviewUrlEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class SystemPreviewSubscriberTest extends TestCase
{
    private const EXAMPLE_URL = 'https://www.example.com';
    private const EXAMPLE_SITE_ACCESS = 'site';
    private const EXAMPLE_LANGUAGE = 'eng-GB';
    private const EXAMPLE_VERSION_NO = 1;
    private const EXAMPLE_CONTENT_ID = 54;

    /** @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface&\PHPUnit\Framework\MockObject\MockObject */
    private UrlGeneratorInterface $urlGenerator;

    private SystemPreviewSubscriber $subscriber;

    protected function setUp(): void
    {
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->subscriber = new SystemPreviewSubscriber($this->urlGenerator);
    }

    public function testOnResolveLocationPreviewUrlDoesNothingWhenPreviewUrlIsAlreadySet(): void
    {
        $event = new ResolveLocationPreviewUrlEvent($this->createMock(Location::class));
        $event->setPreviewUrl(self::EXAMPLE_URL);

        $this->urlGenerator->expects($this->never())->method('generate');

        $this->subscriber->onResolveLocationPreviewUrl($event);

        self::assertEquals(self::EXAMPLE_URL, $event->getPreviewUrl());
    }

    public function testOnResolveLocationPreviewUrlGeneratesPreviewUrl(): void
    {
        $versionInfo = $this->createVersionInfo();
        $content = $this->createContent($versionInfo);
        $location = $this->createLocation($content);

        $this->urlGenerator
            ->method('generate')
            ->with('ibexa.version.preview', [
                'contentId' => self::EXAMPLE_CONTENT_ID,
                'versionNo' => self::EXAMPLE_VERSION_NO,
                'language' => self::EXAMPLE_LANGUAGE,
                'siteAccessName' => self::EXAMPLE_SITE_ACCESS,
            ])
            ->willReturn(self::EXAMPLE_URL);

        $event = new ResolveLocationPreviewUrlEvent($location, [
            'language' => self::EXAMPLE_LANGUAGE,
            'siteaccess' => self::EXAMPLE_SITE_ACCESS,
        ]);

        $this->subscriber->onResolveLocationPreviewUrl($event);

        self::assertEquals(self::EXAMPLE_URL, $event->getPreviewUrl());
    }

    private function createContent(VersionInfo $versionInfo): Content
    {
        $content = $this->createMock(Content::class);
        $content->method('__get')->with('id')->willReturn(self::EXAMPLE_CONTENT_ID);
        $content->method('getVersionInfo')->willReturn($versionInfo);

        return $content;
    }

    private function createLocation(Content $content): Location
    {
        $location = $this->createMock(Location::class);
        $location->method('getContent')->willReturn($content);

        return $location;
    }

    private function createVersionInfo(): VersionInfo
    {
        $versionInfo = $this->createMock(VersionInfo::class);
        $versionInfo->method('__get')->with('versionNo')->willReturn(self::EXAMPLE_VERSION_NO);

        return $versionInfo;
    }
}
