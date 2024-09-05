<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Iterator\BatchIteratorAdapter;

use Ibexa\Contracts\Core\Repository\Iterator\BatchIteratorAdapter;
use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\LanguageSettings;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery;
use Iterator;

final class ProductFetchAdapter implements BatchIteratorAdapter
{
    private ProductServiceInterface $productService;

    private ProductQuery $query;

    private ?LanguageSettings $languageSettings;

    public function __construct(
        ProductServiceInterface $productService,
        ProductQuery $query,
        ?LanguageSettings $languageSettings = null
    ) {
        $this->productService = $productService;
        $this->query = $query;
        $this->languageSettings = $languageSettings;
    }

    /**
     * @return Iterator<\Ibexa\Contracts\ProductCatalog\Values\ProductInterface>
     */
    public function fetch(int $offset, int $limit): Iterator
    {
        $query = clone $this->query;
        $query->setOffset($offset);
        $query->setLimit($limit);

        $iterator = $this->productService->findProducts(
            $query,
            $this->languageSettings
        )->getIterator();

        while (!$iterator instanceof Iterator) {
            $iterator = $iterator->getIterator();
        }

        return $iterator;
    }
}
