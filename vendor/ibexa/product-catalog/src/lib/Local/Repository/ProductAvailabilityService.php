<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository;

use Exception;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Product\Delete;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Product\PreEdit;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Product\View;
use Ibexa\Contracts\ProductCatalog\PermissionResolverInterface;
use Ibexa\Contracts\ProductCatalog\ProductAvailabilityContextResolverInterface;
use Ibexa\Contracts\ProductCatalog\ProductAvailabilityServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Availability\AvailabilityContextInterface;
use Ibexa\Contracts\ProductCatalog\Values\Availability\AvailabilityInterface;
use Ibexa\Contracts\ProductCatalog\Values\Availability\ProductAvailabilityCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Availability\ProductAvailabilityUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\AvailabilityAwareInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\ProductCatalog\Availability\ProductAvailabilityStrategyDispatcher;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductAvailability\HandlerInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\ProductAvailability;
use Ibexa\ProductCatalog\Local\Repository\Values\Availability;

final class ProductAvailabilityService implements ProductAvailabilityServiceInterface
{
    private HandlerInterface $handler;

    private ProductAvailabilityContextResolverInterface $productAvailabilityContextResolver;

    private ProductAvailabilityStrategyDispatcher $productAvailabilityStrategyDispatcher;

    private PermissionResolverInterface $permissionResolver;

    private ProductSpecificationLocator $productSpecificationLocator;

    private Repository $repository;

    public function __construct(
        HandlerInterface $handler,
        ProductAvailabilityContextResolverInterface $productAvailabilityContextResolver,
        ProductAvailabilityStrategyDispatcher $productAvailabilityStrategyDispatcher,
        PermissionResolverInterface $permissionResolver,
        ProductSpecificationLocator $productSpecificationLocator,
        Repository $repository
    ) {
        $this->handler = $handler;
        $this->productAvailabilityContextResolver = $productAvailabilityContextResolver;
        $this->productAvailabilityStrategyDispatcher = $productAvailabilityStrategyDispatcher;
        $this->permissionResolver = $permissionResolver;
        $this->productSpecificationLocator = $productSpecificationLocator;
        $this->repository = $repository;
    }

    public function getAvailability(
        ProductInterface $product,
        ?AvailabilityContextInterface $availabilityContext = null
    ): AvailabilityInterface {
        $this->permissionResolver->assertPolicy(new View($product));

        $context = $this->productAvailabilityContextResolver->resolve($availabilityContext);

        return $this->productAvailabilityStrategyDispatcher->dispatch($product, $context);
    }

    public function hasAvailability(
        ProductInterface $product
    ): bool {
        $this->permissionResolver->assertPolicy(new View($product));

        return $this->handler->exists($product->getCode());
    }

    public function createProductAvailability(
        ProductAvailabilityCreateStruct $struct
    ): AvailabilityInterface {
        $product = $struct->getProduct();

        $this->permissionResolver->assertPolicy(new PreEdit($product));
        $this->assertIsNotBaseProduct($product);

        $this->validateStock($struct->getStock(), $struct->isInfinite());

        $this->repository->beginTransaction();
        try {
            $this->handler->create($struct);

            if ($this->isAvailabilityAwareVariant($product)) {
                $this->updateBaseProductAvailability($product, $struct);
            }

            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }

        $productAvailability = $this->handler->find($product->getCode());

        return $this->buildDomainAvailabilityObject($productAvailability, $product);
    }

    public function updateProductAvailability(
        ProductAvailabilityUpdateStruct $struct
    ): AvailabilityInterface {
        $product = $struct->getProduct();

        $this->permissionResolver->assertPolicy(new PreEdit($product));
        $this->assertIsNotBaseProduct($product);

        $this->validateStock($struct->getStock(), $struct->isInfinite());

        $this->repository->beginTransaction();
        try {
            $this->handler->update($struct);

            if ($this->isAvailabilityAwareVariant($product)) {
                $this->updateBaseProductAvailability($product, $struct);
            }

            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }

        $productAvailability = $this->handler->find($product->getCode());

        return $this->buildDomainAvailabilityObject($productAvailability, $product);
    }

    public function increaseProductAvailability(
        ProductInterface $product,
        int $amount = 1
    ): AvailabilityInterface {
        $this->permissionResolver->assertPolicy(new PreEdit($product));
        $this->assertIsNotBaseProduct($product);

        $productAvailability = $this->handler->find($product->getCode());
        if ($productAvailability->getStock() === null) {
            throw new InvalidArgumentException(
                'amount',
                'Infinite stock cannot be increased'
            );
        }

        $updateStruct = new ProductAvailabilityUpdateStruct(
            $product,
            null,
            null,
            $productAvailability->getStock() + $amount
        );

        $this->repository->beginTransaction();
        try {
            $this->handler->update($updateStruct);

            if ($this->isAvailabilityAwareVariant($product)) {
                $this->updateBaseProductAvailability($product, $updateStruct);
            }

            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }
        $productAvailability = $this->handler->find($product->getCode());

        return $this->buildDomainAvailabilityObject($productAvailability, $product);
    }

    public function decreaseProductAvailability(
        ProductInterface $product,
        int $amount = 1
    ): AvailabilityInterface {
        $this->permissionResolver->assertPolicy(new PreEdit($product));
        $this->assertIsNotBaseProduct($product);

        $productAvailability = $this->handler->find($product->getCode());

        if ($productAvailability->isInfinite()) {
            throw new InvalidArgumentException(
                'amount',
                'Infinite stock cannot be decreased'
            );
        }

        if ($productAvailability->getStock() < $amount) {
            throw new InvalidArgumentException(
                'amount',
                'The stock cannot be reduced to less than zero. Stock may have changed before your request. Please check the current stock in the database.'
            );
        }

        $updateStruct = new ProductAvailabilityUpdateStruct(
            $product,
            null,
            null,
            $productAvailability->getStock() - $amount
        );

        $this->repository->beginTransaction();
        try {
            $this->handler->update($updateStruct);

            if ($this->isAvailabilityAwareVariant($product)) {
                $this->updateBaseProductAvailability($product, $updateStruct);
            }

            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }

        $productAvailability = $this->handler->find($product->getCode());

        return $this->buildDomainAvailabilityObject($productAvailability, $product);
    }

    private function buildDomainAvailabilityObject(
        ProductAvailability $productAvailability,
        ProductInterface $product
    ): Availability {
        return new Availability(
            $product,
            $productAvailability->isAvailable(),
            $productAvailability->isInfinite(),
            $productAvailability->getStock()
        );
    }

    public function deleteProductAvailability(
        ProductInterface $product
    ): void {
        $this->permissionResolver->assertPolicy(new Delete($product));
        if ($product->isBaseProduct()) {
            $this->deleteBaseProductAvailability($product);
        } else {
            $this->repository->beginTransaction();
            try {
                $this->handler->deleteByProductCode($product->getCode());

                if ($this->isAvailabilityAwareVariant($product)) {
                    $this->updateBaseProductAvailability($product);
                }

                $this->repository->commit();
            } catch (Exception $e) {
                $this->repository->rollback();
                throw $e;
            }
        }
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    private function deleteBaseProductAvailability(ProductInterface $baseProduct): void
    {
        $this->handler->deleteByBaseProductCodeWithVariants(
            $baseProduct->getCode(),
            $this->productSpecificationLocator->findField($baseProduct)->id
        );
    }

    /**
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentException
     */
    private function validateStock(?int $stock, ?bool $isInfinite): void
    {
        if ($isInfinite && $stock !== null) {
            throw new InvalidArgumentException(
                'stock',
                'When the product is set as infinite then the stock must be null'
            );
        }
    }

    private function isAvailabilityAwareVariant(ProductInterface $product): bool
    {
        if (!$product instanceof ProductVariantInterface) {
            return false;
        }

        if (!$product->getBaseProduct() instanceof AvailabilityAwareInterface) {
            return false;
        }

        return true;
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\Availability\ProductAvailabilityCreateStruct|\Ibexa\Contracts\ProductCatalog\Values\Availability\ProductAvailabilityUpdateStruct $struct
     */
    private function updateBaseProductAvailability(ProductVariantInterface $productVariant, $struct = null): void
    {
        $baseProduct = $productVariant->getBaseProduct();
        $baseProductCode = $baseProduct->getCode();

        /** @var \Ibexa\Contracts\ProductCatalog\Values\AvailabilityAwareInterface $baseProduct */
        if (!$this->handler->exists($baseProductCode) && $struct !== null) {
            $this->handler->create(
                new ProductAvailabilityCreateStruct(
                    $baseProduct,
                    $struct->getAvailability() ?? false,
                    $struct->isInfinite() ?? false,
                    $struct->getStock()
                )
            );

            return;
        }

        $aggregatedAvailability = $this->handler->findAggregatedForBaseProduct(
            $baseProductCode,
            $this->productSpecificationLocator->findField($baseProduct)->id
        );

        if (
            $struct === null &&
            !$aggregatedAvailability->isAvailable() &&
            !$aggregatedAvailability->isInfinite() &&
            $aggregatedAvailability->getStock() === null
        ) {
            $this->handler->deleteByProductCode($baseProductCode);
        }

        $this->handler->update(
            new ProductAvailabilityUpdateStruct(
                $baseProduct,
                $aggregatedAvailability->isAvailable(),
                $aggregatedAvailability->isInfinite(),
                $aggregatedAvailability->getStock()
            )
        );
    }

    private function assertIsNotBaseProduct(ProductInterface $product): void
    {
        if ($product->isBaseProduct()) {
            throw new InvalidArgumentException(
                'product',
                'Availability cannot be calculated for base product'
            );
        }
    }
}
