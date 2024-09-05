<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\EventSubscriber;

use Ibexa\Contracts\Core\Persistence\Handler as PersistenceHandler;
use Ibexa\Contracts\Core\Search\Handler as SearchHandler;
use Ibexa\Contracts\ProductCatalog\Events\CreatePriceEvent;
use Ibexa\Contracts\ProductCatalog\Events\DeletePriceEvent;
use Ibexa\Contracts\ProductCatalog\Events\ExecutePriceStructsEvent;
use Ibexa\Contracts\ProductCatalog\Events\UpdatePriceEvent;
use Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\ProductCatalog\Config\ConfigProviderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class PriceSearchEventSubscriber implements EventSubscriberInterface
{
    private ConfigProviderInterface $configProvider;

    private SearchHandler $searchHandler;

    private PersistenceHandler $persistenceHandler;

    public function __construct(
        ConfigProviderInterface $configProvider,
        SearchHandler $searchHandler,
        PersistenceHandler $persistenceHandler
    ) {
        $this->configProvider = $configProvider;
        $this->searchHandler = $searchHandler;
        $this->persistenceHandler = $persistenceHandler;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CreatePriceEvent::class => ['onCreatePrice', -10],
            DeletePriceEvent::class => ['onDeletePrice', -10],
            ExecutePriceStructsEvent::class => ['onExecutePriceStructs', -10],
            UpdatePriceEvent::class => ['onUpdatePrice', -10],
        ];
    }

    public function onCreatePrice(CreatePriceEvent $event): void
    {
        if (!$this->isLocalProductCatalog()) {
            return;
        }

        $this->updateIndex($event->getPrice()->getProduct());
    }

    public function onDeletePrice(DeletePriceEvent $event): void
    {
        if (!$this->isLocalProductCatalog()) {
            return;
        }

        $this->updateIndex($event->getDeleteStruct()->getPrice()->getProduct());
    }

    public function onExecutePriceStructs(ExecutePriceStructsEvent $event): void
    {
        if (!$this->isLocalProductCatalog()) {
            return;
        }

        $alreadyUpdatedProducts = [];
        foreach ($event->getPriceStructs() as $struct) {
            $product = $struct->getProduct();
            if (!($product instanceof ContentAwareProductInterface)) {
                continue;
            }

            if (in_array($product->getCode(), $alreadyUpdatedProducts, true)) {
                continue;
            }

            $this->updateIndex($product);
            $alreadyUpdatedProducts[] = $product->getCode();
        }
    }

    public function onUpdatePrice(UpdatePriceEvent $event): void
    {
        if (!$this->isLocalProductCatalog()) {
            return;
        }

        $this->updateIndex($event->getPrice()->getProduct());
    }

    private function updateIndex(ProductInterface $product): void
    {
        if (!($product instanceof ContentAwareProductInterface)) {
            return;
        }

        $versionInfo = $product->getContent()->getVersionInfo();

        $contentId = $versionInfo->getContentInfo()->id;
        $versionNo = $versionInfo->versionNo;

        $this->searchHandler->indexContent(
            $this->persistenceHandler->contentHandler()->load($contentId, $versionNo)
        );

        $locations = $this->persistenceHandler->locationHandler()->loadLocationsByContent($contentId);
        foreach ($locations as $location) {
            $this->searchHandler->indexLocation($location);
        }
    }

    private function isLocalProductCatalog(): bool
    {
        return $this->configProvider->getEngineType() === 'local';
    }
}
