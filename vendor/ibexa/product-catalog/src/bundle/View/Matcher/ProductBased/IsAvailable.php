<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\View\Matcher\ProductBased;

use Ibexa\Contracts\ProductCatalog\Local\LocalProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\ProductAvailabilityServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Contracts\ProductCatalog\ViewMatcher\ProductBased\IsAvailable as IsAvailableInterface;

final class IsAvailable extends AbstractProductMatcher implements IsAvailableInterface
{
    private ProductAvailabilityServiceInterface $availabilityService;

    private bool $value = true;

    public function __construct(
        LocalProductServiceInterface $productService,
        ProductAvailabilityServiceInterface $availabilityService
    ) {
        parent::__construct($productService);

        $this->availabilityService = $availabilityService;
    }

    public function setMatchingConfig($matchingConfig): void
    {
        $this->value = (bool)$matchingConfig;
    }

    protected function matchProduct(ProductInterface $product): bool
    {
        $isAvailable = false;
        if ($this->availabilityService->hasAvailability($product)) {
            $isAvailable = $this->availabilityService->getAvailability($product)->isAvailable();
        }

        return ($this->value && $isAvailable) || (!$this->value && !$isAvailable);
    }
}
