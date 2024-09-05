<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Dashboard\EventSubscriber\PageBuilderViewParameters;

use Ibexa\Core\MVC\Symfony\View\Event\FilterViewParametersEvent;
use Ibexa\Core\MVC\Symfony\View\ViewEvents;
use Ibexa\Tests\Bundle\Dashboard\EventSubscriber\BaseEventSubscriberTestCase;
use Ibexa\Tests\Dashboard\PhpUnit\ConfigResolverMockTrait;
use Ibexa\Tests\Dashboard\PhpUnit\ContentItemOfContentTypeMockTrait;

abstract class BaseDashboardViewParametersSubscriberTestCase extends BaseEventSubscriberTestCase
{
    use ConfigResolverMockTrait;
    use ContentItemOfContentTypeMockTrait;

    protected const PAGE_BUILDER_CONFIG = [
        'foo' => ['bar'],
        'baz' => 1,
    ];

    /**
     * @return iterable<string, array{array<mixed>, callable}>
     */
    abstract public static function getDataForTestOnFilterViewParameters(): iterable;

    /**
     * @return array<array{event: string, priority: int, method: string}>
     */
    abstract protected function getExpectedSubscriberCalls(): array;

    /**
     * @dataProvider getDataForTestOnFilterViewParameters
     *
     * @param array<mixed> $expectedViewParameters
     */
    final public function testOnFilterViewParameters(array $expectedViewParameters, callable $viewMockBuilder): void
    {
        $event = new FilterViewParametersEvent($viewMockBuilder($this), []);
        $this->dispatch($event, ViewEvents::FILTER_VIEW_PARAMETERS, $this->getExpectedSubscriberCalls());

        self::assertSame(
            $expectedViewParameters,
            $event->getViewParameters()
        );
    }
}
