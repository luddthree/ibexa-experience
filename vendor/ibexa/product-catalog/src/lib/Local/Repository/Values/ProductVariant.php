<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Values;

use DateTimeInterface;
use Generator;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Thumbnail;
use Ibexa\Contracts\ProductCatalog\Values\Asset\AssetCollectionInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeInterface;
use Ibexa\Contracts\ProductCatalog\Values\Availability\AvailabilityContextInterface;
use Ibexa\Contracts\ProductCatalog\Values\Availability\AvailabilityInterface;
use Ibexa\Contracts\ProductCatalog\Values\AvailabilityAwareInterface;
use Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\PriceContextInterface;
use Ibexa\Contracts\ProductCatalog\Values\PriceAwareInterface;
use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface;
use Ibexa\ProductCatalog\Exception\IllegalStateException;
use Ibexa\ProductCatalog\Local\Repository\DomainMapper\ProductAvailabilityDelegate;
use Ibexa\ProductCatalog\Local\Repository\DomainMapper\ProductPriceDelegate;
use Ibexa\ProductCatalog\Local\Repository\Values\Asset\AssetCollection;
use Ibexa\ProductCatalog\Local\Repository\Variant\NameGeneratorInterface;

final class ProductVariant implements ProductVariantInterface, AvailabilityAwareInterface, PriceAwareInterface, ContentAwareProductInterface
{
    private ProductInterface $baseProduct;

    private string $code;

    /** @var \Ibexa\Contracts\ProductCatalog\Values\AttributeInterface[] */
    private iterable $attributes;

    private ?ProductAvailabilityDelegate $productAvailabilityDelegate;

    private ?ProductPriceDelegate $productPriceDelegate;

    private ?NameGeneratorInterface $nameGenerator;

    private ?AssetCollectionInterface $assets;

    /**
     * @param iterable<\Ibexa\Contracts\ProductCatalog\Values\AttributeInterface> $attributes
     */
    public function __construct(ProductInterface $baseProduct, string $code, iterable $attributes = [])
    {
        $this->baseProduct = $baseProduct;
        $this->code = $code;
        $this->attributes = $attributes;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        if ($this->nameGenerator === null) {
            return '';
        }

        return $this->nameGenerator->generateName($this);
    }

    public function getProductType(): ProductTypeInterface
    {
        return $this->baseProduct->getProductType();
    }

    public function getThumbnail(): ?Thumbnail
    {
        return $this->baseProduct->getThumbnail();
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->baseProduct->getCreatedAt();
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->baseProduct->getUpdatedAt();
    }

    public function getAttribute(string $identifier): ?AttributeInterface
    {
        foreach ($this->attributes as $attribute) {
            if ($attribute->getIdentifier() === $identifier) {
                return $attribute;
            }
        }

        return null;
    }

    public function getAttributes(): iterable
    {
        yield from $this->baseProduct->getAttributes();
        yield from $this->getDiscriminatorAttributes();
    }

    public function getDiscriminatorAttributes(): iterable
    {
        if ($this->attributes instanceof Generator) {
            // Initialize lazy attributes collection
            $this->attributes = iterator_to_array($this->attributes);
        }

        yield from $this->attributes;
    }

    public function isBaseProduct(): bool
    {
        return false;
    }

    public function isVariant(): bool
    {
        return true;
    }

    public function getBaseProduct(): ProductInterface
    {
        return $this->baseProduct;
    }

    public function getAvailability(?AvailabilityContextInterface $context = null): AvailabilityInterface
    {
        if ($this->productAvailabilityDelegate === null) {
            throw new IllegalStateException(__METHOD__ . ' should to be called before product availability initialization');
        }

        return $this->productAvailabilityDelegate->getAvailability($this, $context);
    }

    public function hasAvailability(): bool
    {
        if ($this->productAvailabilityDelegate === null) {
            throw new IllegalStateException(__METHOD__ . ' should to be called before product availability initialization');
        }

        return $this->productAvailabilityDelegate->hasAvailability($this);
    }

    public function isAvailable(?AvailabilityContextInterface $context = null): bool
    {
        if ($this->hasAvailability()) {
            return $this->getAvailability($context)->isAvailable();
        }

        return false;
    }

    public function setProductAvailabilityDelegate(ProductAvailabilityDelegate $productAvailabilityDelegate): void
    {
        $this->productAvailabilityDelegate = $productAvailabilityDelegate;
    }

    public function setNameGenerator(NameGeneratorInterface $generator): void
    {
        $this->nameGenerator = $generator;
    }

    public function getPrice(?PriceContextInterface $context = null): ?PriceInterface
    {
        if ($this->productPriceDelegate === null) {
            throw new IllegalStateException(__METHOD__ . ' should to be called after product price initialization');
        }

        $price = $this->productPriceDelegate->getPrice($this, $context);
        if ($price === null) {
            $price = $this->productPriceDelegate->getPrice($this->getBaseProduct(), $context);
        }

        return $price;
    }

    public function setProductPriceDelegate(ProductPriceDelegate $productPriceDelegate): void
    {
        $this->productPriceDelegate = $productPriceDelegate;
    }

    public function getContent(): Content
    {
        /** @var \Ibexa\ProductCatalog\Local\Repository\Values\Product $product */
        $product = $this->getBaseProduct();

        return $product->getContent();
    }

    public function getAssets(): AssetCollectionInterface
    {
        return $this->assets ?? new AssetCollection();
    }

    public function setAssets(?AssetCollectionInterface $assets): void
    {
        $this->assets = $assets;
    }
}
