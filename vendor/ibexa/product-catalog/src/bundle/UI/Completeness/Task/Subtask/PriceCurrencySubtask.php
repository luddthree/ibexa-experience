<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\Subtask;

use Ibexa\Bundle\ProductCatalog\UI\Completeness\Entry\BooleanEntry;
use Ibexa\Bundle\ProductCatalog\UI\Completeness\Entry\EntryInterface;
use Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\AbstractTask;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Symfony\Component\Routing\RouterInterface;

final class PriceCurrencySubtask extends AbstractTask
{
    private CurrencyInterface $currency;

    private RouterInterface $router;

    /** @var \Ibexa\Contracts\ProductCatalog\Values\PriceInterface[] */
    private array $definedPrices;

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\PriceInterface[] $definedPrices
     */
    public function __construct(
        CurrencyInterface $currency,
        RouterInterface $router,
        array $definedPrices
    ) {
        $this->currency = $currency;
        $this->router = $router;
        $this->definedPrices = $definedPrices;
    }

    public function getIdentifier(): string
    {
        return $this->currency->getCode() . '_price_currency_task';
    }

    public function getEntry(ProductInterface $product): ?EntryInterface
    {
        $currencyCode = $this->currency->getCode();
        foreach ($this->definedPrices as $price) {
            if ($price->getCurrency()->getCode() === $currencyCode) {
                return new BooleanEntry(true);
            }
        }

        return new BooleanEntry(false);
    }

    public function getName(): string
    {
        return $this->currency->getCode();
    }

    /**
     * @return array<\Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\TaskGroup>|null
     */
    public function getSubtaskGroups(ProductInterface $product): ?array
    {
        return null;
    }

    /**
     * @phpstan-return int<1, max>
     */
    public function getWeight(): int
    {
        return 1;
    }

    public function getEditUrl(ProductInterface $product): ?string
    {
        /** @var \Ibexa\Bundle\ProductCatalog\UI\Completeness\Entry\BooleanEntry $entry */
        $entry = $this->getEntry($product);

        $route = $entry->isComplete() ?
            'ibexa.product_catalog.product.price.update' :
            'ibexa.product_catalog.product.price.create';

        return $this->router->generate($route, [
            'productCode' => $product->getCode(),
            'currencyCode' => $this->currency->getCode(),
        ]);
    }
}
