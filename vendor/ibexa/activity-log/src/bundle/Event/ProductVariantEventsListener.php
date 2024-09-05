<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ActivityLog\Event;

use Ibexa\Contracts\ActivityLog\ActivityLogServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\Events;
use Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ProductVariantEventsListener implements EventSubscriberInterface
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
            Events\CreateProductVariantsEvent::class => ['onProductVariantsCreate'],
            Events\UpdateProductVariantEvent::class => ['onProductVariantUpdate'],

            Events\DeleteBaseProductVariantsEvent::class => ['onBaseProductDelete'],
        ];
    }

    public function onProductVariantsCreate(Events\CreateProductVariantsEvent $event): void
    {
        // TODO: Prefer database ID
        $product = $event->getProduct();
        $this->saveActivityLog('create', $product->getCode(), $product->getName());
    }

    public function onProductVariantUpdate(Events\UpdateProductVariantEvent $event): void
    {
        // TODO: Prefer database ID
        $productVariant = $event->getProductVariant();
        $this->saveActivityLog('update', $productVariant->getCode(), $productVariant->getName());
    }

    public function onBaseProductDelete(Events\DeleteBaseProductVariantsEvent $event): void
    {
        // TODO: Prefer database ID
        $product = $event->getBaseProduct();
        $this->saveActivityLog('delete', $product->getCode(), $product->getName());
    }

    private function saveActivityLog(string $action, string $id, string $name): void
    {
        $activityLog = $this->activityLogService->build(ProductVariantInterface::class, $id, $action);
        $activityLog->setObjectName($name);
        $this->activityLogService->save($activityLog);
    }
}
