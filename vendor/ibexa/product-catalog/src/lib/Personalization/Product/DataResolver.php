<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Personalization\Product;

use Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface;
use Ibexa\Personalization\Content\DataResolverInterface as ContentDataResolverInterface;

final class DataResolver implements DataResolverInterface
{
    private ContentDataResolverInterface $contentDataResolver;

    /** @var \Ibexa\ProductCatalog\Personalization\Product\DataProviderInterface[]|iterable */
    private iterable $dataProviders;

    /**
     * @param iterable<\Ibexa\ProductCatalog\Personalization\Product\DataProviderInterface> $dataProviders
     */
    public function __construct(
        ContentDataResolverInterface $contentDataResolver,
        iterable $dataProviders
    ) {
        $this->contentDataResolver = $contentDataResolver;
        $this->dataProviders = $dataProviders;
    }

    /**
     * @return array<string, scalar|array<scalar|null>|null>
     */
    public function resolve(ContentAwareProductInterface $product, string $languageCode): array
    {
        $content = $product->getContent();
        $data[] = $this->contentDataResolver->resolve($content, $languageCode);

        foreach ($this->dataProviders as $dataProvider) {
            $productData = $dataProvider->getData($product, $languageCode);
            if ($productData !== null) {
                $data[] = $productData;
            }
        }

        return array_merge(...$data);
    }
}
