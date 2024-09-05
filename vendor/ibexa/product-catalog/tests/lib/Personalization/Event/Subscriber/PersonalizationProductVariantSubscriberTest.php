<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Personalization\Event\Subscriber;

use Ibexa\Personalization\Event\PersonalizationUserTrackingRenderOptionsEvent;
use Ibexa\ProductCatalog\Personalization\Event\Subscriber\PersonalizationProductVariantSubscriber;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class PersonalizationProductVariantSubscriberTest extends AbstractCoreEventSubscriberTest
{
    private PersonalizationProductVariantSubscriber $productVariantSubscriber;

    public function setUp(): void
    {
        parent::setUp();

        $this->productVariantSubscriber = new PersonalizationProductVariantSubscriber();
    }

    public function getEventSubscriber(): EventSubscriberInterface
    {
        return $this->productVariantSubscriber;
    }

    /**
     * @return iterable<int, array<int, string>>
     */
    public function subscribedEventsDataProvider(): iterable
    {
        return [
            [PersonalizationUserTrackingRenderOptionsEvent::class],
        ];
    }

    public function testCallOnUserTrackingRender(): void
    {
        $event = new PersonalizationUserTrackingRenderOptionsEvent(
            [
                'variantCode' => 'variant_code',
                'contentId' => 14,
            ]
        );

        /** @var \Ibexa\ProductCatalog\Personalization\Event\Subscriber\PersonalizationProductVariantSubscriber $productVariantSubscriber */
        $productVariantSubscriber = $this->getEventSubscriber();

        $productVariantSubscriber->onUserTrackingRender($event);

        self::assertEquals(
            [
                'contentId' => 'variant_code',
                'variantCode' => 'variant_code',
            ],
            $event->getOptions()
        );
    }

    public function testCallOnUserTrackingRenderWithoutVariantCode(): void
    {
        $event = new PersonalizationUserTrackingRenderOptionsEvent(
            [
                'contentId' => 14,
            ]
        );

        self::assertEquals(
            [
                'contentId' => 14,
            ],
            $event->getOptions()
        );
    }
}
