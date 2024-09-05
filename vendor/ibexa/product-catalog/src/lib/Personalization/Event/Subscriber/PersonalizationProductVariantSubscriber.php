<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Personalization\Event\Subscriber;

use Ibexa\Bundle\Personalization\Templating\Twig\Functions\UserTracking;
use Ibexa\Personalization\Event\PersonalizationUserTrackingRenderOptionsEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class PersonalizationProductVariantSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            PersonalizationUserTrackingRenderOptionsEvent::class => ['onUserTrackingRender', 255],
        ];
    }

    public function onUserTrackingRender(PersonalizationUserTrackingRenderOptionsEvent $event): void
    {
        $variantCode = $event->getOptions()[UserTracking::VARIANT_CODE_KEY] ?? null;

        if ($variantCode === null) {
            return;
        }

        $event->setOption(UserTracking::CONTENT_ID_KEY, $variantCode);
    }
}
