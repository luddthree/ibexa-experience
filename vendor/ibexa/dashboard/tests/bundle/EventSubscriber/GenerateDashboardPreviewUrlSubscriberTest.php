<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Dashboard\EventSubscriber;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\PageBuilder\Event\GenerateContentPreviewUrlEvent;
use Ibexa\Dashboard\EventSubscriber\GenerateDashboardPreviewUrlSubscriber;
use Ibexa\Tests\Dashboard\PhpUnit\ConfigResolverMockTrait;
use Ibexa\Tests\Dashboard\PhpUnit\ContentItemOfContentTypeMockTrait;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @covers \Ibexa\Dashboard\EventSubscriber\GenerateDashboardPreviewUrlSubscriber
 */
final class GenerateDashboardPreviewUrlSubscriberTest extends BaseEventSubscriberTestCase
{
    use ContentItemOfContentTypeMockTrait;
    use ConfigResolverMockTrait;

    private const ORIGINAL_PARAMETERS = ['bar' => 1];

    protected function buildEventSubscribers(): iterable
    {
        yield new GenerateDashboardPreviewUrlSubscriber($this->mockConfigResolver());
    }

    /**
     * @return iterable<string, array{\Ibexa\Contracts\Core\Repository\Values\Content\Content, array<mixed>}>
     */
    public function getDataForTestOnGenerateContentPreviewUrl(): iterable
    {
        yield 'dashboard content' => [
            $this->mockContentItemOfDashboardType(),
            self::ORIGINAL_PARAMETERS + ['viewType' => 'dashboard'],
        ];

        yield 'folder content' => [
            $this->mockContentItemOfContentType('folder'),
            self::ORIGINAL_PARAMETERS,
        ];
    }

    public function testGetSubscribedEvents(): void
    {
        $subscribedEvents = GenerateDashboardPreviewUrlSubscriber::getSubscribedEvents();
        self::assertArrayHasKey(
            GenerateContentPreviewUrlEvent::NAME,
            $subscribedEvents
        );
    }

    /**
     * @dataProvider getDataForTestOnGenerateContentPreviewUrl
     *
     * @param array<mixed> $expectedParameters
     */
    public function testOnGenerateContentPreviewUrl(Content $content, array $expectedParameters): void
    {
        $event = new GenerateContentPreviewUrlEvent(
            $content,
            'foo',
            self::ORIGINAL_PARAMETERS,
            UrlGeneratorInterface::ABSOLUTE_PATH
        );
        $this->dispatch(
            $event,
            GenerateContentPreviewUrlEvent::NAME,
            [
                [
                    'event' => GenerateContentPreviewUrlEvent::NAME,
                    'priority' => 0,
                    'method' => GenerateDashboardPreviewUrlSubscriber::class . '::onGenerateContentPreviewUrl',
                ],
            ]
        );

        self::assertSame($expectedParameters, $event->getParameters());
    }
}
