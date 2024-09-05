<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Personalization\Event\Subscriber;

use Ibexa\Contracts\Core\Repository\Iterator\BatchIterator;
use Ibexa\Contracts\ProductCatalog\Events\CreatePriceEvent;
use Ibexa\Contracts\ProductCatalog\Events\DeletePriceEvent;
use Ibexa\Contracts\ProductCatalog\Events\ExecutePriceStructsEvent;
use Ibexa\Contracts\ProductCatalog\Events\UpdatePriceEvent;
use Ibexa\Contracts\ProductCatalog\Iterator\BatchIteratorAdapter\VariantFetchAdapter;
use Ibexa\Contracts\ProductCatalog\Local\Events\BeforeDeleteBaseProductVariantsEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\CreateProductVariantsEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\DeleteProductEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\UpdateProductEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\UpdateProductVariantEvent;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Product\Delete;
use Ibexa\Contracts\ProductCatalog\PermissionResolverInterface;
use Ibexa\Contracts\ProductCatalog\ProductServiceInterface as APIProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface;
use Ibexa\Personalization\Exception\InvalidArgumentException;
use Ibexa\Personalization\Service\Content\ContentServiceInterface;
use Ibexa\ProductCatalog\Personalization\Service\Product\ProductServiceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ProductEventSubscriber implements EventSubscriberInterface
{
    private const PRIORITY = -10;

    private ContentServiceInterface $contentService;

    private ProductServiceInterface $productService;

    private APIProductServiceInterface $apiProductService;

    private PermissionResolverInterface $permissionResolver;

    public function __construct(
        ContentServiceInterface $contentService,
        ProductServiceInterface $productService,
        APIProductServiceInterface $apiProductService,
        PermissionResolverInterface $permissionResolver
    ) {
        $this->contentService = $contentService;
        $this->productService = $productService;
        $this->apiProductService = $apiProductService;
        $this->permissionResolver = $permissionResolver;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CreatePriceEvent::class => ['onCreatePrice', self::PRIORITY],
            UpdatePriceEvent::class => ['onUpdatePrice', self::PRIORITY],
            DeletePriceEvent::class => ['onDeletePrice', self::PRIORITY],
            UpdateProductEvent::class => ['onUpdateProduct', self::PRIORITY],
            DeleteProductEvent::class => ['onDeleteProduct', self::PRIORITY],
            BeforeDeleteBaseProductVariantsEvent::class => ['onBeforeDeleteBaseProductVariants', self::PRIORITY],
            CreateProductVariantsEvent::class => ['onCreateProductVariants', self::PRIORITY],
            UpdateProductVariantEvent::class => ['onUpdateProductVariant', self::PRIORITY],
            ExecutePriceStructsEvent::class => ['onExecutePriceStructs', self::PRIORITY],
        ];
    }

    public function onCreatePrice(CreatePriceEvent $event): void
    {
        $this->sendNotificationForProduct($event->getCreateStruct()->getProduct());
    }

    public function onDeletePrice(DeletePriceEvent $event): void
    {
        $this->sendNotificationForProduct($event->getDeleteStruct()->getProduct());
    }

    public function onUpdateProduct(UpdateProductEvent $event): void
    {
        $product = $event->getProduct();

        if (!$product->isBaseProduct()) {
            return;
        }

        $variantIterator = new BatchIterator(
            new VariantFetchAdapter($this->apiProductService, $product),
            25
        );

        $this->productService->updateVariants(iterator_to_array($variantIterator));
    }

    public function onDeleteProduct(DeleteProductEvent $event): void
    {
        $product = $event->getProduct();

        if ($product->isVariant()) {
            /** @var \Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface $product */
            $this->sendNotificationForDeleteVariant($product);
        }
    }

    public function onBeforeDeleteBaseProductVariants(BeforeDeleteBaseProductVariantsEvent $event): void
    {
        $baseProduct = $event->getBaseProduct();

        if (!$this->permissionResolver->canUser(new Delete($baseProduct)) || !$baseProduct->isBaseProduct()) {
            return;
        }

        $variantIterator = new BatchIterator(
            new VariantFetchAdapter($this->apiProductService, $baseProduct),
            25
        );

        $this->productService->deleteVariants(iterator_to_array($variantIterator));
    }

    public function onCreateProductVariants(CreateProductVariantsEvent $event): void
    {
        $createStructs = $event->getCreateStructs();

        $variants = [];
        foreach ($createStructs as $createStruct) {
            $code = $createStruct->getCode();
            if ($code === null) {
                throw new InvalidArgumentException('ProductVariant code must be provided');
            }

            $variants[] = $this->apiProductService->getProductVariant($code);
        }

        $this->productService->updateVariants($variants);
    }

    public function onUpdateProductVariant(UpdateProductVariantEvent $event): void
    {
        $productVariant = $event->getProductVariant();

        $this->sendNotificationForUpdateVariant($productVariant);
    }

    public function onUpdatePrice(UpdatePriceEvent $event): void
    {
        $this->sendNotificationForProduct($event->getUpdateStruct()->getProduct());
    }

    public function onExecutePriceStructs(ExecutePriceStructsEvent $event): void
    {
        $productCodes = [];
        $products = [];

        foreach ($event->getPriceStructs() as $struct) {
            $product = $struct->getProduct();
            $productCode = $product->getCode();

            if (
                $product instanceof ContentAwareProductInterface
                && !in_array($productCode, $productCodes, true)
            ) {
                $productCodes[] = $productCode;
                $products[] = $product->getContent();
            }
        }

        $this->contentService->updateContentItems($products);
    }

    private function sendNotificationForProduct(ProductInterface $product): void
    {
        if (!$product instanceof ContentAwareProductInterface) {
            return;
        }

        $this->contentService->updateContent($product->getContent());
    }

    private function sendNotificationForDeleteVariant(ProductVariantInterface $productVariant): void
    {
        if (!$productVariant instanceof ContentAwareProductInterface) {
            return;
        }

        $this->productService->deleteVariant($productVariant);
    }

    private function sendNotificationForUpdateVariant(
        ProductVariantInterface $productVariant
    ): void {
        if (!$productVariant instanceof ContentAwareProductInterface) {
            return;
        }

        $this->productService->updateVariant($productVariant);
    }
}
