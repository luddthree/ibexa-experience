<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Twig;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\ProductCatalog\Local\LocalProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\ProductCatalog\Local\Repository\Attribute\ValueFormatterDispatcherInterface;
use Twig\Extension\RuntimeExtensionInterface;

final class RenderProductRuntime implements RuntimeExtensionInterface
{
    private LocalProductServiceInterface $productService;

    private ValueFormatterDispatcherInterface $formatterDispatcher;

    public function __construct(
        LocalProductServiceInterface $productService,
        ValueFormatterDispatcherInterface $formatterDispatcher
    ) {
        $this->productService = $productService;
        $this->formatterDispatcher = $formatterDispatcher;
    }

    /**
     * @param mixed $value
     */
    public function getProduct($value): ?ProductInterface
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof ProductInterface) {
            return $value;
        }

        if ($value instanceof Content) {
            return $this->productService->getProductFromContent($value);
        }

        return null;
    }

    /**
     * @param mixed $value
     */
    public function isProduct($value): bool
    {
        if ($value === null) {
            return false;
        }

        if ($value instanceof ProductInterface) {
            return true;
        }

        if ($value instanceof Content) {
            return $this->productService->isProduct($value);
        }

        return false;
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function formatAttributeValue(?AttributeInterface $attribute, array $parameters = []): ?string
    {
        if ($attribute === null) {
            return null;
        }

        return $this->formatterDispatcher->formatValue($attribute, $parameters);
    }
}
