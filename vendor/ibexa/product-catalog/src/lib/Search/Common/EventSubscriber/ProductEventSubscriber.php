<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Search\Common\EventSubscriber;

use Ibexa\Contracts\ProductCatalog\Local\Events\CreateProductAvailabilityEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\DecreaseProductAvailabilityEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\DeleteProductAvailabilityEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\IncreaseProductAvailabilityEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\UpdateProductAvailabilityEvent;
use Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Core\Search\Common\EventSubscriber\AbstractSearchEventSubscriber;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ProductEventSubscriber extends AbstractSearchEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            CreateProductAvailabilityEvent::class => 'onCreateAvailability',
            DeleteProductAvailabilityEvent::class => 'onDeleteAvailability',
            UpdateProductAvailabilityEvent::class => 'onUpdateAvailability',
            IncreaseProductAvailabilityEvent::class => 'onIncreaseAvailability',
            DecreaseProductAvailabilityEvent::class => 'onDecreaseAvailability',
        ];
    }

    public function onCreateAvailability(CreateProductAvailabilityEvent $event): void
    {
        $this->indexProduct($event->getCreateStruct()->getProduct());
    }

    public function onDeleteAvailability(DeleteProductAvailabilityEvent $event): void
    {
        $this->indexProduct($event->getProduct());
    }

    public function onUpdateAvailability(UpdateProductAvailabilityEvent $event): void
    {
        $this->indexProduct($event->getUpdateStruct()->getProduct());
    }

    public function onIncreaseAvailability(IncreaseProductAvailabilityEvent $event): void
    {
        $this->indexProduct($event->getProduct());
    }

    public function onDecreaseAvailability(DecreaseProductAvailabilityEvent $event): void
    {
        $this->indexProduct($event->getProduct());
    }

    private function indexProduct(ProductInterface $product): void
    {
        if (!$product instanceof ContentAwareProductInterface) {
            return;
        }

        $content = $product->getContent();
        $contentId = $content->getVersionInfo()->getContentInfo()->id;
        $this->searchHandler->indexContent(
            $this->persistenceHandler->contentHandler()->load(
                $contentId,
                $content->getVersionInfo()->versionNo
            )
        );

        $locations = $this->persistenceHandler->locationHandler()->loadLocationsByContent(
            $contentId
        );

        foreach ($locations as $location) {
            $this->searchHandler->indexLocation($location);
        }
    }
}
