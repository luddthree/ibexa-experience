<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Dashboard\EventSubscriber\PageBuilder;

use Ibexa\Bundle\Dashboard\EventSubscriber\PageBuilder\BlockQueryParametersSubscriber;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\BlockRenderEvents;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Event\PreRenderEvent;
use Ibexa\Tests\Bundle\Dashboard\EventSubscriber\BaseEventSubscriberTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @covers \Ibexa\Bundle\Dashboard\EventSubscriber\PageBuilder\BlockQueryParametersSubscriber
 */
final class BlockQueryParametersSubscriberTest extends BaseEventSubscriberTestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject&\Symfony\Component\HttpFoundation\RequestStack */
    private RequestStack $requestStackMock;

    protected function buildEventSubscribers(): iterable
    {
        $this->requestStackMock = $this->createMock(RequestStack::class);

        yield new BlockQueryParametersSubscriber($this->requestStackMock, ['foo_block']);
    }

    public function testGetSubscribedEvents(): void
    {
        $subscribedEventNames = BlockQueryParametersSubscriber::getSubscribedEvents();
        self::assertArrayHasKey(BlockRenderEvents::GLOBAL_BLOCK_RENDER_PRE, $subscribedEventNames);
    }

    /**
     * @return iterable<string, array{Request, Request, array<string, mixed>}>
     */
    public function getDataForTestOnBlockPreRender(): iterable
    {
        // main request, block sub-request, expected parameters array
        yield 'add new parameters to an empty block sub-request' => [
            new Request(['foo' => 'bar']),
            new Request(),
            ['foo' => 'bar'],
        ];

        yield 'add new parameters to a non-empty block sub-request' => [
            new Request(['foo' => 'bar']),
            new Request(['xyz' => 'abc']),
            ['xyz' => 'abc', 'foo' => 'bar'],
        ];

        yield 'override block sub-request parameters' => [
            new Request(['foo' => 'bar']),
            new Request(['foo' => 'baz']),
            ['foo' => 'bar'],
        ];
    }

    /**
     * @dataProvider getDataForTestOnBlockPreRender
     *
     * @param array<string, mixed> $expectedParameters
     */
    public function testOnBlockPreRender(
        Request $mainRequest,
        Request $blockSubRequest,
        array $expectedParameters
    ): void {
        $preRenderEventMock = $this->createPreRenderEventMock();

        $this->requestStackMock->method('getMainRequest')->willReturn($mainRequest);
        $this->requestStackMock->method('getCurrentRequest')->willReturn($blockSubRequest);

        $this->dispatch(
            $preRenderEventMock,
            BlockRenderEvents::GLOBAL_BLOCK_RENDER_PRE,
            null
        );

        self::assertSame($expectedParameters, $blockSubRequest->query->all());
    }

    private function createPreRenderEventMock(): PreRenderEvent
    {
        $blockValueMock = $this->createMock(BlockValue::class);
        $blockValueMock->method('getType')->willReturn('foo_block');
        $blockValueMock->method('getId')->willReturn('123');

        $preRenderEventMock = $this->createMock(PreRenderEvent::class);
        $preRenderEventMock
            ->expects(self::once())
            ->method('getBlockValue')
            ->willReturn($blockValueMock)
        ;

        return $preRenderEventMock;
    }
}
