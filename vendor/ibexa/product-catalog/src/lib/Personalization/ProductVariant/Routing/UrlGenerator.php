<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Personalization\ProductVariant\Routing;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\LanguageSettings;
use Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface;
use Ibexa\Personalization\Config\Host\HostResolverInterface;

/**
 * @internal
 */
final class UrlGenerator implements UrlGeneratorInterface
{
    private const PRODUCT_VARIANT_URL_PREFIX = '%s/api/ibexa/v2/personalization/v1/product_variant/%s/%s%s';
    private const PRODUCT_VARIANT_CODE_URL_PREFIX = 'code';
    private const PRODUCT_VARIANT_LIST_URL_PREFIX = 'list';

    private HostResolverInterface $hostResolver;

    private ProductServiceInterface $productService;

    public function __construct(
        HostResolverInterface $hostResolver,
        ProductServiceInterface $productService
    ) {
        $this->hostResolver = $hostResolver;
        $this->productService = $productService;
    }

    public function generate(
        ProductVariantInterface $productVariant,
        ?string $lang = null
    ): string {
        return $this->getProductVariantUrl(
            $productVariant,
            self::PRODUCT_VARIANT_CODE_URL_PREFIX,
            $productVariant->getCode(),
            $lang ?? $this->getBaseContent($productVariant)->getDefaultLanguageCode()
        );
    }

    /**
     * @param string[] $variantCodes
     */
    public function generateForVariantCodes(
        array $variantCodes,
        string $lang
    ): string {
        $variantCode = $variantCodes[0];
        $productVariant = $this->productService->getProductVariant(
            $variantCode,
            new LanguageSettings([$lang])
        );

        return $this->getProductVariantUrl(
            $productVariant,
            self::PRODUCT_VARIANT_LIST_URL_PREFIX,
            implode(',', $variantCodes),
            $lang
        );
    }

    private function getProductVariantUrl(
        ProductVariantInterface $productVariant,
        string $prefix,
        string $variantCodes,
        string $lang
    ): string {
        $baseContent = $this->getBaseContent($productVariant);

        return sprintf(
            self::PRODUCT_VARIANT_URL_PREFIX,
            $this->hostResolver->resolveUrl($baseContent, $lang),
            $prefix,
            $variantCodes,
            '?lang=' . $lang
        );
    }

    private function getBaseContent(ProductVariantInterface $productVariant): Content
    {
        /** @var \Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface $baseProduct */
        $baseProduct = $productVariant->getBaseProduct();

        return $baseProduct->getContent();
    }
}
