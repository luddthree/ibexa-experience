<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Dashboard\EventSubscriber\PageBuilderViewParameters;

use Ibexa\ContentForms\Content\View\ContentEditView;
use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\Core\MVC\Symfony\View\ViewEvents;
use Ibexa\Dashboard\EventSubscriber\PageBuilderViewParameters\DashboardEditViewParametersSubscriber;

/**
 * @covers \Ibexa\Dashboard\EventSubscriber\PageBuilderViewParameters\DashboardEditViewParametersSubscriber
 */
final class DashboardEditViewParametersSubscriberTest extends BaseDashboardViewParametersSubscriberTestCase
{
    protected function getExpectedSubscriberCalls(): array
    {
        return [
            [
                'event' => ViewEvents::FILTER_VIEW_PARAMETERS,
                'priority' => -100,
                'method' => DashboardEditViewParametersSubscriber::class . '::onFilterViewParameters',
            ],
        ];
    }

    protected function buildEventSubscribers(): iterable
    {
        yield new DashboardEditViewParametersSubscriber($this->mockConfigResolver(), self::PAGE_BUILDER_CONFIG);
    }

    public function testGetSubscribedEvents(): void
    {
        self::assertArrayHasKey(
            ViewEvents::FILTER_VIEW_PARAMETERS,
            DashboardEditViewParametersSubscriber::getSubscribedEvents()
        );
    }

    public static function getDataForTestOnFilterViewParameters(): iterable
    {
        yield 'dashboard content edit view' => [
            ['page_builder_config' => self::PAGE_BUILDER_CONFIG],
            static function (self $self): View {
                $viewMock = $self->createMock(ContentEditView::class);
                $viewMock->method('getContent')->willReturn($self->mockContentItemOfDashboardType());

                return $viewMock;
            },
        ];

        yield 'folder content edit view' => [
            [],
            static function (self $self): View {
                $viewMock = $self->createMock(ContentEditView::class);
                $viewMock->method('getContent')->willReturn($self->mockContentItemOfContentType('folder'));

                return $viewMock;
            },
        ];

        yield 'not content edit view' => [
            [],
            static function (self $self): View {
                return $self->createMock(View::class);
            },
        ];
    }
}
