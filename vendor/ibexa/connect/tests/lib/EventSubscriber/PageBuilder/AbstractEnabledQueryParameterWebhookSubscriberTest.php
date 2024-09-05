<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Connect\EventSubscriber\PageBuilder;

use Ibexa\Bundle\Connect\Event\PrePageBlockWebhookRequestEvent;
use Ibexa\Bundle\Connect\EventSubscriber\PageBuilder\AbstractEnabledQueryParameterWebhookSubscriber;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Attribute;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeDefinition;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition;
use Ibexa\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeFactoryInterface;
use Ibexa\FieldTypePage\FieldType\Page\Block\Definition\Event\BlockDefinitionEvent;
use PHPUnit\Framework\TestCase;

final class AbstractEnabledQueryParameterWebhookSubscriberTest extends TestCase
{
    /**
     * @dataProvider provideForTestOnBlockRender
     */
    public function testOnBlockRender(?Attribute $attribute, bool $enabled): void
    {
        $event = $this->getPreRenderEvent($attribute);

        $expectedQuery = null;
        if ($enabled) {
            $expectedQuery = [
                '<QUERY_PARAMETER_NAME>' => 'foo',
            ];
        }

        $subscriber = $this->getSubscriberForPreRender($enabled);

        $subscriber->onBlockRender($event);

        self::assertSame($expectedQuery, $event->getQuery());
    }

    private function getPreRenderEvent(?Attribute $attribute): PrePageBlockWebhookRequestEvent
    {
        $attributes = [
            'foo' => new Attribute('foo', 'Foo', 'bar'),
        ];
        if ($attribute !== null) {
            $attributes['<ATTRIBUTE_IDENTIFIER>'] = $attribute;
        }

        $blockValue = new BlockValue(
            'id',
            'ibexa_connect',
            'Ibexa Connect',
            'foo',
            null,
            null,
            null,
            null,
            null,
            $attributes,
        );

        return new PrePageBlockWebhookRequestEvent($blockValue);
    }

    /**
     * @param mixed $attributeValue
     */
    private function getAttribute($attributeValue): Attribute
    {
        return new Attribute('420', '<ATTRIBUTE_IDENTIFIER>', $attributeValue);
    }

    /**
     * @return iterable<array{
     *     \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\Attribute,
     *     bool,
     * }>
     */
    public function provideForTestOnBlockRender(): iterable
    {
        yield [
            $this->getAttribute(null),
            false,
        ];

        yield [
            $this->getAttribute('0'),
            false,
        ];

        yield [
            $this->getAttribute(''),
            false,
        ];

        yield [
            $this->getAttribute(false),
            false,
        ];

        yield [
            $this->getAttribute('1'),
            true,
        ];

        yield [
            $this->getAttribute(true),
            true,
        ];
    }

    private function getSubscriberForPreRender(bool $enabled): AbstractEnabledQueryParameterWebhookSubscriber
    {
        $subscriber = $this->createMock(AbstractEnabledQueryParameterWebhookSubscriber::class);
        $subscriber
            ->expects(self::once())
            ->method('getAttributeIdentifier')
            ->willReturn('<ATTRIBUTE_IDENTIFIER>');
        $subscriber
            ->expects(self::never())
            ->method('getAttributeName');
        $subscriber
            ->expects($enabled ? self::once() : self::never())
            ->method('resolveQueryParameter')
            ->willReturn('foo');
        $subscriber
            ->expects($enabled ? self::once() : self::never())
            ->method('getQueryParameterName')
            ->willReturn('<QUERY_PARAMETER_NAME>');

        return $subscriber;
    }

    public function testBlockDefinition(): void
    {
        $blockDefinition = new BlockDefinition();
        $event = new BlockDefinitionEvent($blockDefinition, []);

        $subscriber = $this->getMockBuilder(AbstractEnabledQueryParameterWebhookSubscriber::class)
            ->setConstructorArgs([$this->createMock(BlockAttributeFactoryInterface::class)])
            ->getMock();

        $subscriber
            ->expects(self::once())
            ->method('getAttributeIdentifier')
            ->willReturn('<ATTRIBUTE_IDENTIFIER>');
        $subscriber
            ->expects(self::once())
            ->method('getAttributeName')
            ->willReturn('<ATTRIBUTE_NAME>');
        $subscriber
            ->expects(self::never())
            ->method('resolveQueryParameter')
            ->willReturn('foo');
        $subscriber
            ->expects(self::never())
            ->method('getQueryParameterName');

        $subscriber->onBlockDefinition($event);

        $attributes = $blockDefinition->getAttributes();
        self::assertArrayHasKey('<ATTRIBUTE_IDENTIFIER>', $attributes);
        self::assertInstanceOf(BlockAttributeDefinition::class, $attributes['<ATTRIBUTE_IDENTIFIER>']);
    }
}
