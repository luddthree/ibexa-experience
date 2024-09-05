<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Dashboard\EventSubscriber\PageBuilder;

use Ibexa\AdminUi\Form\Type\Content\Draft\ContentEditType;
use Ibexa\Bundle\Dashboard\EventSubscriber\PageBuilder\ContentBlocksSubscriber;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\BlockRenderEvents;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Event\PreRenderEvent;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Twig\TwigRenderRequest;
use Ibexa\Tests\Bundle\Dashboard\EventSubscriber\BaseEventSubscriberTestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @covers \Ibexa\Bundle\Dashboard\EventSubscriber\PageBuilder\ContentBlocksSubscriber
 */
final class ContentBlocksSubscriberTest extends BaseEventSubscriberTestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject&\Symfony\Component\Form\FormFactoryInterface */
    private FormFactoryInterface $formFactoryMock;

    protected function buildEventSubscribers(): iterable
    {
        $this->formFactoryMock = $this->createMock(FormFactoryInterface::class);

        $urlGeneratorMock = $this->createMock(UrlGeneratorInterface::class);
        $urlGeneratorMock->method('generate')->with('ibexa.content.edit')->willReturn('/foo');

        yield new ContentBlocksSubscriber(
            $this->formFactoryMock,
            $urlGeneratorMock
        );
    }

    public function testGetSubscribedEvents(): void
    {
        $subscribedEventNames = array_keys(ContentBlocksSubscriber::getSubscribedEvents());
        self::assertCount(2, $subscribedEventNames);
        self::assertStringContainsString('my_content', $subscribedEventNames[0]);
        self::assertStringContainsString('common_content', $subscribedEventNames[1]);
    }

    /**
     * @return iterable<string, array{string, string, string, null|array<array{event: string, priority: int, method: string}>}>
     */
    public function getDataForTestOnBlockPreRender(): iterable
    {
        yield 'My content block' => [
            'my_content',
            'my_content',
            'content_edit_my_content_123',
            [
                [
                    'event' => BlockRenderEvents::getBlockPreRenderEventName('my_content'),
                    'priority' => -100,
                    'method' => ContentBlocksSubscriber::class . '::onBlockPreRender',
                ],
            ],
        ];

        yield 'Common content block' => [
            'common_content',
            'common_content',
            'content_edit_common_content_123',
            [
                [
                    'event' => BlockRenderEvents::getBlockPreRenderEventName('common_content'),
                    'priority' => -100,
                    'method' => ContentBlocksSubscriber::class . '::onBlockPreRender',
                ],
            ],
        ];

        yield 'Block with invalid type' => [
            'my_content',
            'my invalid  Block $type',
            'content_edit_my_invalid__Block__type_123',
            null,
        ];
    }

    /**
     * @dataProvider getDataForTestOnBlockPreRender
     *
     * @param array<array{event: string, priority: int, method: string}>|null $expectedSubscriberCalls
     */
    public function testOnBlockPreRender(
        string $blockIdentifier,
        string $blockType,
        string $expectedFormName,
        ?array $expectedSubscriberCalls
    ): void {
        $twigRenderRequestMock = $this->createMock(TwigRenderRequest::class);
        $preRenderEventMock = $this->createPreRenderEventMock($twigRenderRequestMock, $blockType);

        $contentEditForm = $this->createMock(FormInterface::class);
        $this->formFactoryMock
            ->expects(self::once())
            ->method('createNamed')
            ->with(
                $expectedFormName,
                ContentEditType::class
            )
            ->willReturn($contentEditForm)
        ;
        $formViewMock = $this->createMock(FormView::class);
        $contentEditForm
            ->expects(self::once())
            ->method('createView')
            ->willReturn($formViewMock)
        ;
        $twigRenderRequestMock
            ->expects(self::once())
            ->method('addParameter')
            ->with('form_edit', $formViewMock)
        ;

        $this->dispatch(
            $preRenderEventMock,
            BlockRenderEvents::getBlockPreRenderEventName($blockIdentifier),
            $expectedSubscriberCalls
        );
    }

    /**
     * @return iterable<string, array{Request, Request, array<string, mixed>}>
     */
    public function getDataForTestOnBlockPreRenderForwardsQueryParameters(): iterable
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

    private function createPreRenderEventMock(
        TwigRenderRequest $twigRenderRequestMock,
        string $blockType
    ): PreRenderEvent {
        $blockValueMock = $this->createMock(BlockValue::class);
        $blockValueMock->method('getType')->willReturn($blockType);
        $blockValueMock->method('getId')->willReturn('123');

        $preRenderEventMock = $this->createMock(PreRenderEvent::class);
        $preRenderEventMock
            ->expects(self::once())
            ->method('getRenderRequest')
            ->willReturn($twigRenderRequestMock)
        ;
        $preRenderEventMock
            ->expects(self::once())
            ->method('getBlockValue')
            ->willReturn($blockValueMock)
        ;

        return $preRenderEventMock;
    }
}
