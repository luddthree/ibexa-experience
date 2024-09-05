<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\EventSubscriber;

use Ibexa\Contracts\Core\Repository\Events\Content\BeforeDeleteContentEvent;
use Ibexa\Contracts\ProductCatalog\Local\LocalProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\ProductAvailabilityServiceInterface;
use Ibexa\Contracts\ProductCatalog\ProductPriceServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\Delete\Struct\ProductPriceDeleteStruct;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Core\Repository\ContentService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class DeleteProductSubscriber implements EventSubscriberInterface
{
    private ProductPriceServiceInterface $priceService;

    private ProductAvailabilityServiceInterface $productAvailabilityService;

    private ContentService $contentService;

    private LocalProductServiceInterface $localProductService;

    public function __construct(
        ProductPriceServiceInterface $priceService,
        ProductAvailabilityServiceInterface $productAvailabilityService,
        LocalProductServiceInterface $localProductService,
        ContentService $contentService
    ) {
        $this->priceService = $priceService;
        $this->productAvailabilityService = $productAvailabilityService;
        $this->localProductService = $localProductService;
        $this->contentService = $contentService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeDeleteContentEvent::class => 'onBeforeDeleteContent',
        ];
    }

    public function onBeforeDeleteContent(BeforeDeleteContentEvent $event): void
    {
        // works in the context of `sudo` method
        $contentInfo = $event->getContentInfo();
        $content = $this->contentService->loadContent($contentInfo->id);

        if (!$this->localProductService->isProduct($content)) {
            return;
        }

        $product = $this->localProductService->getProductFromContent($content);
        if ($product->isBaseProduct()) {
            $this->deletePricesForVariants($product);
        }

        $this->deletePrices($product->getCode());

        $this->productAvailabilityService->deleteProductAvailability($product);
    }

    private function deletePricesForVariants(ProductInterface $baseProduct): void
    {
        foreach ($this->localProductService->findProductVariants($baseProduct) as $variant) {
            $this->deletePrices($variant->getCode());
        }
    }

    private function deletePrices(string $productCode): void
    {
        $prices = $this->priceService->findPricesByProductCode($productCode);

        foreach ($prices as $price) {
            $struct = new ProductPriceDeleteStruct($price);
            $this->priceService->deletePrice($struct);
        }
    }
}
