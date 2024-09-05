<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\EventSubscriber;

use Ibexa\Contracts\ProductCatalog\Events\ProductCodeChangedEvent;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductAvailability\HandlerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class UpdateAvailabilityAfterProductCodeChange implements EventSubscriberInterface
{
    private HandlerInterface $handler;

    public function __construct(HandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProductCodeChangedEvent::class => 'updateProductCodeForAvailability',
        ];
    }

    public function updateProductCodeForAvailability(ProductCodeChangedEvent $event): void
    {
        $this->handler->updateProductCode($event->getNewCode(), $event->getOldCode());
    }
}
