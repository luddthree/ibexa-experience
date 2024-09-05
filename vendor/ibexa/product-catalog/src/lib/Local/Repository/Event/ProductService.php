<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Event;

use Ibexa\Contracts\ProductCatalog\Local\Events\BeforeCreateProductEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\BeforeCreateProductVariantsEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\BeforeDeleteBaseProductVariantsEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\BeforeDeleteProductEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\BeforeUpdateProductEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\BeforeUpdateProductVariantEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\CreateProductEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\CreateProductVariantsEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\DeleteBaseProductVariantsEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\DeleteProductEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\UpdateProductEvent;
use Ibexa\Contracts\ProductCatalog\Local\Events\UpdateProductVariantEvent;
use Ibexa\Contracts\ProductCatalog\Local\LocalProductServiceDecorator;
use Ibexa\Contracts\ProductCatalog\Local\LocalProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductCreateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductVariantUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class ProductService extends LocalProductServiceDecorator
{
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        LocalProductServiceInterface $innerService,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($innerService);

        $this->eventDispatcher = $eventDispatcher;
    }

    public function createProduct(ProductCreateStruct $createStruct): ProductInterface
    {
        $beforeEvent = new BeforeCreateProductEvent($createStruct);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getResultProduct();
        }

        $product = $beforeEvent->hasResultProduct()
            ? $beforeEvent->getResultProduct()
            : $this->innerService->createProduct($createStruct);

        $this->eventDispatcher->dispatch(new CreateProductEvent($createStruct, $product));

        return $product;
    }

    public function createProductVariants(ProductInterface $product, iterable $createStructs): void
    {
        $beforeEvent = new BeforeCreateProductVariantsEvent($product, $createStructs);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->createProductVariants($product, $createStructs);

        $this->eventDispatcher->dispatch(new CreateProductVariantsEvent($product, $createStructs));
    }

    public function deleteProduct(ProductInterface $product): void
    {
        $beforeEvent = new BeforeDeleteProductEvent($product);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->deleteProduct($product);

        $this->eventDispatcher->dispatch(new DeleteProductEvent($product));
    }

    public function deleteProductVariantsByBaseProduct(ProductInterface $baseProduct): array
    {
        $beforeEvent = new BeforeDeleteBaseProductVariantsEvent($baseProduct);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return [];
        }

        $deletedVariantCodes = $this->innerService->deleteProductVariantsByBaseProduct($baseProduct);
        $this->eventDispatcher->dispatch(
            new DeleteBaseProductVariantsEvent($baseProduct, $deletedVariantCodes)
        );

        return $deletedVariantCodes;
    }

    public function updateProduct(ProductUpdateStruct $updateStruct): ProductInterface
    {
        $beforeEvent = new BeforeUpdateProductEvent($updateStruct);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getResultProduct();
        }

        $product = $beforeEvent->hasResultProduct()
            ? $beforeEvent->getResultProduct()
            : $this->innerService->updateProduct($updateStruct);

        $this->eventDispatcher->dispatch(new UpdateProductEvent($product, $updateStruct));

        return $product;
    }

    public function updateProductVariant(
        ProductVariantInterface $productVariant,
        ProductVariantUpdateStruct $updateStruct
    ): ProductVariantInterface {
        $beforeEvent = new BeforeUpdateProductVariantEvent($productVariant, $updateStruct);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getResultProductVariant();
        }

        $productVariant = $beforeEvent->hasResultProductVariant()
            ? $beforeEvent->getResultProductVariant()
            : $this->innerService->updateProductVariant($productVariant, $updateStruct);

        $this->eventDispatcher->dispatch(new UpdateProductVariantEvent($productVariant, $updateStruct));

        return $productVariant;
    }
}
