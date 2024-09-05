<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ActivityLog\Event;

use Ibexa\Contracts\ActivityLog\ActivityLogServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\Events;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ProductEventsListener implements EventSubscriberInterface
{
    private ActivityLogServiceInterface $activityLogService;

    public function __construct(
        ActivityLogServiceInterface $activityLogService
    ) {
        $this->activityLogService = $activityLogService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events\CreateProductEvent::class => ['onProductCreate'],
            Events\UpdateProductEvent::class => ['onProductUpdate'],
            Events\DeleteProductEvent::class => ['onProductDelete'],
        ];
    }

    public function onProductCreate(Events\CreateProductEvent $event): void
    {
        // TODO: Prefer database ID
        $product = $event->getProduct();
        $this->saveActivityLog('create', $product->getCode(), $product->getName());
    }

    public function onProductUpdate(Events\UpdateProductEvent $event): void
    {
        // TODO: Prefer database ID
        $product = $event->getProduct();
        $this->saveActivityLog('update', $product->getCode(), $product->getName());
    }

    public function onProductDelete(Events\DeleteProductEvent $event): void
    {
        // TODO: Prefer database ID
        $product = $event->getProduct();
        $this->saveActivityLog('delete', $product->getCode(), $product->getName());
    }

    private function saveActivityLog(string $action, string $id, string $name): void
    {
        $activityLog = $this->activityLogService->build(ProductInterface::class, $id, $action);
        $activityLog->setObjectName($name);
        $this->activityLogService->save($activityLog);
    }
}
