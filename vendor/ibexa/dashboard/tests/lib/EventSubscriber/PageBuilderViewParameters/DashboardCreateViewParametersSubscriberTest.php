<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Dashboard\EventSubscriber\PageBuilderViewParameters;

use Ibexa\ContentForms\Content\View\ContentCreateView;
use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\Core\MVC\Symfony\View\ViewEvents;
use Ibexa\Dashboard\EventSubscriber\PageBuilderViewParameters\DashboardCreateViewParametersSubscriber;

/**
 * @covers \Ibexa\Dashboard\EventSubscriber\PageBuilderViewParameters\DashboardCreateViewParametersSubscriber
 */
final class DashboardCreateViewParametersSubscriberTest extends BaseDashboardViewParametersSubscriberTestCase
{
    protected function getExpectedSubscriberCalls(): array
    {
        return [
            [
                'event' => ViewEvents::FILTER_VIEW_PARAMETERS,
                'priority' => -100,
                'method' => DashboardCreateViewParametersSubscriber::class . '::onFilterViewParameters',
            ],
        ];
    }

    protected function buildEventSubscribers(): iterable
    {
        yield new DashboardCreateViewParametersSubscriber(
            $this->mockConfigResolver(),
            self::PAGE_BUILDER_CONFIG
        );
    }

    public function testGetSubscribedEvents(): void
    {
        self::assertArrayHasKey(
            ViewEvents::FILTER_VIEW_PARAMETERS,
            DashboardCreateViewParametersSubscriber::getSubscribedEvents()
        );
    }

    public static function getDataForTestOnFilterViewParameters(): iterable
    {
        yield 'dashboard content create view' => [
            ['page_builder_config' => self::PAGE_BUILDER_CONFIG],
            static function (self $self): View {
                $viewMock = $self->createMock(ContentCreateView::class);
                $viewMock->method('getContentType')->willReturn($self->mockDashboardContentType());

                return $viewMock;
            },
        ];

        yield 'folder content create view' => [
            [],
            static function (self $self): View {
                $viewMock = $self->createMock(ContentCreateView::class);
                $viewMock->method('getContentType')->willReturn($self->mockContentType('folder'));

                return $viewMock;
            },
        ];

        yield 'not content create view' => [
            [],
            static function (self $self): View {
                return $self->createMock(View::class);
            },
        ];
    }
}
