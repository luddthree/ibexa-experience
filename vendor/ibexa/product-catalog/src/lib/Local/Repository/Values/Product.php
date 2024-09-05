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
use Ibexa\Contracts\ProductCatalog\Values\Availability\AvailabilityContextInterface;
use Ibexa\Contracts\ProductCatalog\Values\Availability\AvailabilityInterface;
use Ibexa\Contracts\ProductCatalog\Values\AvailabilityAwareInterface;
use Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\PriceContextInterface;
use Ibexa\Contracts\ProductCatalog\Values\PriceAwareInterface;
use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductVariantListInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductVariantQuery;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductVariantsAwareInterface;
use Ibexa\Contracts\ProductCatalog\Values\TranslatableInterface;
use Ibexa\ProductCatalog\Exception\IllegalStateException;
use Ibexa\ProductCatalog\Local\Repository\DomainMapper\ProductAvailabilityDelegate;
use Ibexa\ProductCatalog\Local\Repository\DomainMapper\ProductPriceDelegate;
use Ibexa\ProductCatalog\Local\Repository\DomainMapper\ProductVariantsDelegate;
use Ibexa\ProductCatalog\Local\Repository\Values\Asset\AssetCollection;

/**
 * @final
 */
class Product implements ContentAwareProductInterface, AvailabilityAwareInterface, PriceAwareInterface, ProductVariantsAwareInterface, TranslatableInterface
{
    private ProductTypeInterface $productType;

    private Content $content;

    private string $code;

    /** @var iterable<\Ibexa\Contracts\ProductCatalog\Values\AttributeInterface> */
    private iterable $attributes;

    private ?ProductAvailabilityDelegate $productAvailabilityDelegate;

    private ?ProductPriceDelegate $productPriceDelegate;

    private ?ProductVariantsDelegate $productVariantsDelegate;

    private ?AssetCollectionInterface $assets;

    private ?bool $isBaseProduct = null;

    /**
     * @param iterable<\Ibexa\Contracts\ProductCatalog\Values\AttributeInterface> $attributes
     */
    public function __construct(
        ProductTypeInterface $productType,
        Content $content,
        string $code,
        iterable $attributes = []
    ) {
        $this->productType = $productType;
        $this->content = $content;
        $this->code = $code;
        $this->attributes = $attributes;
    }

    public function getName(): string
    {
        $name = $this->content->getName();

        assert(is_string($name));

        return $name;
    }

    public function getThumbnail(): ?Thumbnail
    {
        return $this->content->getThumbnail();
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getContent(): Content
    {
        return $this->content;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->content->versionInfo->creationDate;
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->content->contentInfo->modificationDate;
    }

    public function getProductType(): ProductTypeInterface
    {
        return $this->productType;
    }

    /**
     * @return iterable<\Ibexa\Contracts\ProductCatalog\Values\AttributeInterface>
     */
    public function getAttributes(): iterable
    {
        if ($this->attributes instanceof Generator) {
            // Initialize lazy attributes collection
            $this->attributes = iterator_to_array($this->attributes);
        }

        return $this->attributes;
    }

    public function isBaseProduct(): bool
    {
        if ($this->isBaseProduct === null) {
            $this->isBaseProduct = $this->computeBaseProductFlag();
        }

        return $this->isBaseProduct;
    }

    private function computeBaseProductFlag(): bool
    {
        foreach ($this->getProductType()->getAttributesDefinitions() as $attribute) {
            if ($attribute->isDiscriminator()) {
                return true;
            }
        }

        return false;
    }

    public function isVariant(): bool
    {
        return false;
    }

    public function getAvailability(?AvailabilityContextInterface $context = null): AvailabilityInterface
    {
        if ($this->productAvailabilityDelegate === null) {
            throw new IllegalStateException(__METHOD__ . ' should be called before product availability initialization');
        }

        return $this->productAvailabilityDelegate->getAvailability($this, $context);
    }

    public function hasAvailability(): bool
    {
        if ($this->productAvailabilityDelegate === null) {
            throw new IllegalStateException(__METHOD__ . ' should be called before product availability initialization');
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

    public function getPrice(?PriceContextInterface $context = null): ?PriceInterface
    {
        if ($this->productPriceDelegate === null) {
            throw new IllegalStateException(__METHOD__ . ' should be called after product price initialization');
        }

        return $this->productPriceDelegate->getPrice($this, $context);
    }

    public function setProductPriceDelegate(ProductPriceDelegate $productPriceDelegate): void
    {
        $this->productPriceDelegate = $productPriceDelegate;
    }

    public function getVariantsList(?ProductVariantQuery $query = null): ProductVariantListInterface
    {
        if ($this->productVariantsDelegate === null) {
            throw new IllegalStateException(__METHOD__ . ' should be called after product variants initialization');
        }

        return $this->productVariantsDelegate->getVariants($this, $query);
    }

    public function setProductVariantsDelegate(ProductVariantsDelegate $productVariantsDelegate): void
    {
        $this->productVariantsDelegate = $productVariantsDelegate;
    }

    public function getAssets(): AssetCollectionInterface
    {
        return $this->assets ?? new AssetCollection();
    }

    public function setAssets(?AssetCollectionInterface $assets): void
    {
        $this->assets = $assets;
    }

    public function getLanguages(): array
    {
        return $this->content->getVersionInfo()->languageCodes;
    }
}
