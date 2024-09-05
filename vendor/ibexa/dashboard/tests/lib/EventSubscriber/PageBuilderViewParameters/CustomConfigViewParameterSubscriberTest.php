<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Dashboard\EventSubscriber\PageBuilderViewParameters;

use Ibexa\ContentForms\Content\View\ContentEditView;
use Ibexa\Core\MVC\Symfony\View\Event\FilterViewParametersEvent;
use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\Core\MVC\Symfony\View\ViewEvents;
use Ibexa\Dashboard\EventSubscriber\PageBuilderViewParameters\DashboardEditViewParametersSubscriber;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @covers \Ibexa\Dashboard\EventSubscriber\PageBuilderViewParameters\BaseDashboardViewParametersSubscriber
 */
final class CustomConfigViewParameterSubscriberTest extends BaseDashboardViewParametersSubscriberTestCase
{
    protected function getExpectedSubscriberCalls(): array
    {
        return [
            [
                'event' => ViewEvents::FILTER_VIEW_PARAMETERS,
                'priority' => 0,
                'method' => EventSubscriberInterface::class . '@anonymous::onFilterViewParameters',
            ],
            [
                'event' => ViewEvents::FILTER_VIEW_PARAMETERS,
                'priority' => -100,
                'method' => DashboardEditViewParametersSubscriber::class . '::onFilterViewParameters',
            ],
        ];
    }

    protected function buildEventSubscribers(): iterable
    {
        yield new class() implements EventSubscriberInterface {
            public static function getSubscribedEvents(): array
            {
                return [
                    ViewEvents::FILTER_VIEW_PARAMETERS => 'onFilterViewParameters',
                ];
            }

            public function onFilterViewParameters(FilterViewParametersEvent $event): void
            {
                $event->getParameterBag()->add(
                    [
                        'page_builder_config' => [
                            'my_custom_setting' => 'my_custom_value',
                            'baz' => 2,
                        ],
                    ]
                );
            }
        };

        yield new DashboardEditViewParametersSubscriber($this->mockConfigResolver(), self::PAGE_BUILDER_CONFIG);
    }

    public static function getDataForTestOnFilterViewParameters(): iterable
    {
        yield 'merged dashboard config and custom config' => [
            [
                'page_builder_config' => [
                    'foo' => ['bar'],
                    'baz' => 2, // dashboard subscriber respects overridden setting
                    'my_custom_setting' => 'my_custom_value',
                ],
            ],
            static function (self $self): View {
                $viewMock = $self->createMock(ContentEditView::class);
                $viewMock->method('getContent')->willReturn($self->mockContentItemOfDashboardType());

                return $viewMock;
            },
        ];
    }

    public function testGetSubscribedEvents(): void
    {
        self::markTestSkipped('Test irrelevant for the use case');
    }
}
