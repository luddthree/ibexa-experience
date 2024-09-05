<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Personalization\Product;

use Ibexa\Contracts\Core\Repository\URLAliasService;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;

final class VariantDataProvider implements DataProviderInterface
{
    private const DATA_KEY = 'product_base_content_id';
    private const VARIANT_URI = 'uri';

    private URLAliasService $urlAliasService;

    public function __construct(URLAliasService $urlAliasService)
    {
        $this->urlAliasService = $urlAliasService;
    }

    public function getData(ContentAwareProductInterface $product, string $languageCode): ?array
    {
        /** @var \Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface $product */
        if (!$product->isVariant()) {
            return null;
        }

        $baseProduct = $product->getBaseProduct();

        if (!$baseProduct instanceof ContentAwareProductInterface) {
            throw new InvalidArgumentException('product', 'must be an instance of ' . ContentAwareProductInterface::class);
        }

        $content = $baseProduct->getContent();
        $location = $content->getVersionInfo()->getContentInfo()->getMainLocation();
        if (null === $location) {
            return null;
        }

        $uri = $this->getUrl($location, $languageCode);
        $queryData = ['variant' => $product->getCode()];

        return [
            self::VARIANT_URI => $uri . '?' . http_build_query($queryData),
            self::DATA_KEY => $content->id,
        ];
    }

    private function getUrl(Location $location, string $languageCode): ?string
    {
        foreach ($this->getListLocationAliases($location, $languageCode) as $urlAlias) {
            return $urlAlias->path;
        }

        return null;
    }

    /**
     * @return iterable<\Ibexa\Contracts\Core\Repository\Values\Content\URLAlias>
     */
    private function getListLocationAliases(Location $location, string $languageCode): iterable
    {
        $prioritizedLanguagesList = [];

        foreach ($location->getContent()->getVersionInfo()->getLanguages() as $language) {
            $prioritizedLanguagesList[] = $language->getLanguageCode();
        }

        yield from $this->urlAliasService->listLocationAliases(
            $location,
            false,
            $languageCode,
            true,
            $prioritizedLanguagesList
        );
    }
}
