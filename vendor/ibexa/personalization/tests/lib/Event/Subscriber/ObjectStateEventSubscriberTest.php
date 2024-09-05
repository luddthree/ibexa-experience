<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Event\Subscriber;

use Ibexa\Contracts\Core\Repository\ContentService as CoreContentService;
use Ibexa\Contracts\Core\Repository\Events\ObjectState\SetContentStateEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectState;
use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroup;
use Ibexa\Personalization\Config\ItemType\IncludedItemTypeResolverInterface;
use Ibexa\Personalization\Event\Subscriber\ObjectStateEventSubscriber;
use Ibexa\Personalization\Service\Content\ContentServiceInterface;
use Ibexa\Personalization\Service\Setting\SettingServiceInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @covers \Ibexa\Personalization\Event\Subscriber\ObjectStateEventSubscriber
 */
final class ObjectStateEventSubscriberTest extends TestCase
{
    /** @var \Ibexa\Contracts\Core\Repository\ContentService&\PHPUnit\Framework\MockObject\MockObject */
    private CoreContentService $coreContentService;

    /** @var \Ibexa\Personalization\Service\Content\ContentServiceInterface&\PHPUnit\Framework\MockObject\MockObject */
    private ContentServiceInterface $contentService;

    /** @var \Symfony\Component\EventDispatcher\EventSubscriberInterface&\Ibexa\Personalization\Event\Subscriber\ObjectStateEventSubscriber */
    private EventSubscriberInterface $eventSubscriber;

    /** @var \Ibexa\Personalization\Config\ItemType\IncludedItemTypeResolverInterface&\PHPUnit\Framework\MockObject\MockObject */
    private IncludedItemTypeResolverInterface $includedItemTypeResolver;

    protected function setUp(): void
    {
        $this->coreContentService = $this->createMock(CoreContentService::class);
        $this->contentService = $this->createMock(ContentServiceInterface::class);
        $this->includedItemTypeResolver = $this->createMock(IncludedItemTypeResolverInterface::class);
        $settingService = $this->createMock(SettingServiceInterface::class);
        $settingService->method('isInstallationKeyFound')->willReturn(true);

        $this->eventSubscriber = new ObjectStateEventSubscriber(
            $this->coreContentService,
            $this->contentService,
            $this->includedItemTypeResolver,
            $settingService
        );
    }

    public function testOnSetContentState(): void
    {
        $content = $this->createMock(Content::class);
        $contentInfo = $this->createMock(ContentInfo::class);

        $this->mockCoreContentServiceLoadContentByContentInfo($contentInfo, $content);
        $this->mockIncludedItemTypeResolverIsContentIncluded($content);
        $this->mockContentServiceUpdateContent($content);

        $this->eventSubscriber->onSetContentState(
            new SetContentStateEvent(
                $contentInfo,
                $this->createMock(ObjectStateGroup::class),
                $this->createMock(ObjectState::class)
            )
        );
    }

    private function mockIncludedItemTypeResolverIsContentIncluded(Content $content): void
    {
        $this->includedItemTypeResolver
            ->expects(self::once())
            ->method('isContentIncluded')
            ->with($content)
            ->willReturn(true);
    }

    private function mockCoreContentServiceLoadContentByContentInfo(
        ContentInfo $contentInfo,
        Content $content
    ): void {
        $this->coreContentService
            ->expects(self::once())
            ->method('loadContentByContentInfo')
            ->with($contentInfo)
            ->willReturn($content);
    }

    private function mockContentServiceUpdateContent(Content $content): void
    {
        $this->contentService
            ->expects(self::once())
            ->method('updateContent')
            ->with($content)
            ->willReturnSelf();
    }
}
