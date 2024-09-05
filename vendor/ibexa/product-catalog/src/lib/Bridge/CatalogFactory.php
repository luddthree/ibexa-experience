<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Bridge;

use Ibexa\Bundle\Commerce\Eshop\Catalog\CatalogElement;
use Ibexa\Bundle\Commerce\Eshop\Catalog\CatalogNode;
use Ibexa\Bundle\Commerce\Eshop\Content\Fields\ImageField;
use Ibexa\Bundle\Commerce\Eshop\Content\Fields\PriceField;
use Ibexa\Bundle\Commerce\Eshop\Content\Fields\StockField;
use Ibexa\Bundle\Commerce\Eshop\Model\Navigation\UrlMapping;
use Ibexa\Bundle\Commerce\Eshop\Product\ComplexProductNode;
use Ibexa\Bundle\Commerce\Eshop\Product\OrderableProductNode;
use Ibexa\Bundle\Commerce\Eshop\Product\ProductType;
use Ibexa\Bundle\Commerce\Eshop\Product\VariantProductNode;
use Ibexa\Bundle\Commerce\Eshop\Services\Factory\CatalogFactory as BaseCatalogFactory;
use Ibexa\Bundle\Commerce\Eshop\Services\Url\CatalogUrl;
use Ibexa\Bundle\Commerce\Price\Model\Price;
use Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException;
use Ibexa\Contracts\ProductCatalog\RegionResolverInterface;
use Ibexa\Contracts\ProductCatalog\Values\AvailabilityAwareInterface;
use Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\PriceAwareInterface;
use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\VatCategoryInterface;
use Ibexa\Contracts\ProductCatalog\VatCalculatorInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentType;
use Ibexa\ProductCatalog\Money\DecimalMoneyFactory;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @deprecated since 4.6, will be removed in 5.0. Use ibexa/checkout and ibexa/order-management packages instead
 */
final class CatalogFactory extends BaseCatalogFactory
{
    private RegionResolverInterface $regionResolver;

    private CatalogUrl $urlService;

    private UrlGeneratorInterface $urlGenerator;

    private VatCalculatorInterface $vatCalculator;

    private DecimalMoneyFactory $decimalMoneyFactory;

    public function __construct(
        RegionResolverInterface $regionResolver,
        CatalogUrl $urlService,
        UrlGeneratorInterface $urlGenerator,
        VatCalculatorInterface $vatCalculator,
        DecimalMoneyFactory $decimalMoneyFactory
    ) {
        $this->regionResolver = $regionResolver;
        $this->urlService = $urlService;
        $this->urlGenerator = $urlGenerator;
        $this->vatCalculator = $vatCalculator;
        $this->decimalMoneyFactory = $decimalMoneyFactory;
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\ProductInterface|null $data
     */
    public function createCatalogNode($data = null): CatalogNode
    {
        if ($data instanceof ProductInterface) {
            return new CatalogNode($this->getCatalogNodeProperties($data), $this->urlService);
        }

        throw new InvalidArgumentType('$data', ProductInterface::class);
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\ProductInterface|null $data
     */
    public function createOrderableProductNode($data = null): OrderableProductNode
    {
        if ($data instanceof ProductInterface) {
            return new OrderableProductNode($this->getOrderableNodeProperties($data), $this->urlService);
        }

        throw new InvalidArgumentType('$data', ProductInterface::class);
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\ProductInterface|null $data
     */
    public function createComplexProductNode($data = null): ComplexProductNode
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\ProductInterface|null $data
     */
    public function createVariantProductNode($data = null): VariantProductNode
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @param array<mixed> $nodeInfo
     */
    public function createCatalogElement(array $nodeInfo = []): CatalogElement
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\ProductInterface|null $data
     */
    public function createProductTypeNode($data = null): ProductType
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @param array<string,mixed> $params
     */
    public function setCatalogUrlMapping(UrlMapping $urlMapping, array $params = []): void
    {
        throw new NotImplementedException(__METHOD__);
    }

    /**
     * @return array<string,mixed>
     */
    private function getCatalogNodeProperties(ProductInterface $product): array
    {
        $properties = [];
        $properties['name'] = $product->getName();
        $properties['identifier'] = $product->getCode();

        $image = $this->getImageField($product);
        if ($image !== null) {
            $properties['image'] = $image;
        }

        if ($product instanceof ContentAwareProductInterface) {
            $properties['url'] = $this->getProductUrl($product);
        }

        return $properties;
    }

    /**
     * @return array<string,mixed>
     */
    private function getOrderableNodeProperties(ProductInterface $product): array
    {
        $properties = [];
        $properties['name'] = $product->getName();
        $properties['identifier'] = $product->getCode();
        $properties['sku'] = $product->getCode();

        $stock = $this->getStockField($product);
        if ($stock !== null) {
            $properties['stock'] = $stock;
        }

        $price = $this->getPriceField($product);
        if ($price !== null) {
            $properties['price'] = $price;
        }

        $image = $this->getImageField($product);
        if ($image !== null) {
            $properties['image'] = $image;
        }

        if ($product instanceof ContentAwareProductInterface) {
            $properties['url'] = $this->getProductUrl($product);
        }

        return $properties;
    }

    private function getImageField(ProductInterface $product): ?ImageField
    {
        $thumbnail = $product->getThumbnail();
        if ($thumbnail === null) {
            return null;
        }

        return  new ImageField([
            'fileName' => basename($thumbnail->resource),
            'path' => dirname($thumbnail->resource),
        ]);
    }

    private function getPriceField(ProductInterface $product): ?PriceField
    {
        if (!($product instanceof PriceAwareInterface)) {
            return null;
        }

        $price = $product->getPrice();
        if ($price === null) {
            return null;
        }

        $vatCategory = $product->getProductType()->getVatCategory($this->regionResolver->resolveRegion());
        if ($vatCategory === null) {
            return null;
        }

        $grossPrice = $this->calculateGrossPrice($price, $vatCategory);

        return new PriceField([
            'price' => new Price([
                'price' => $grossPrice,
                'priceInclVat' => $grossPrice,
                'priceExclVat' => (float)$price->getAmount(),
                'isVatPrice' => true,
                'vatPercent' => $vatCategory->getVatValue(),
                'currency' => $price->getCurrency()->getCode(),
                'source' => PriceProvider::SOURCE_TYPE,
            ]),
        ]);
    }

    private function getStockField(ProductInterface $product): ?StockField
    {
        if ($product instanceof AvailabilityAwareInterface && $product->hasAvailability()) {
            $availability = $product->getAvailability();

            if ($availability->isAvailable()) {
                return new StockField([
                    'stockNumeric' => $availability->isInfinite() ? PHP_INT_MAX : $availability->getStock(),
                    'stockSign' => null,
                ]);
            }
        }

        return null;
    }

    private function calculateGrossPrice(PriceInterface $price, ?VatCategoryInterface $vatCategory): ?float
    {
        if ($vatCategory !== null) {
            $grossPrice = $price->getMoney()->add(
                $this->vatCalculator->calculate($price, $vatCategory)
            );

            return (float) $this->decimalMoneyFactory->getMoneyFormatter()->format($grossPrice);
        }

        return null;
    }

    private function getProductUrl(ContentAwareProductInterface $product): string
    {
        return $this->urlGenerator->generate(
            'ibexa.url.alias',
            [
                'contentId' => $product->getContent()->id,
            ]
        );
    }
}
