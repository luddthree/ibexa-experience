<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\UI\Completeness\Task;

use Ibexa\Bundle\ProductCatalog\UI\Completeness\Entry\EntryInterface;
use Ibexa\Bundle\ProductCatalog\UI\Completeness\Entry\PercentEntry;
use Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\Subtask\PriceCurrencySubtask;
use Ibexa\Contracts\Core\Repository\Iterator\BatchIterator;
use Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface;
use Ibexa\Contracts\ProductCatalog\Iterator\BatchIteratorAdapter\CurrencyFetchAdapter;
use Ibexa\Contracts\ProductCatalog\ProductPriceServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyQuery;
use Ibexa\Contracts\ProductCatalog\Values\Currency\Query\Criterion\IsCurrencyEnabledCriterion;
use Ibexa\Contracts\ProductCatalog\Values\PriceAwareInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class PricesTask extends AbstractTask
{
    private const CURRENCIES_LOAD_BATCH_SIZE = 200;

    private ProductPriceServiceInterface $priceService;

    private CurrencyServiceInterface $currencyService;

    private TranslatorInterface $translator;

    private RouterInterface $router;

    public function __construct(
        ProductPriceServiceInterface $priceService,
        CurrencyServiceInterface $currencyService,
        TranslatorInterface $translator,
        RouterInterface $router
    ) {
        $this->priceService = $priceService;
        $this->currencyService = $currencyService;
        $this->translator = $translator;
        $this->router = $router;
    }

    public function getIdentifier(): string
    {
        return 'prices';
    }

    public function getName(): string
    {
        return $this->translator->trans(/** @Desc("Prices") */ 'product.completeness.prices.label', [], 'ibexa_product_catalog');
    }

    public function getEntry(ProductInterface $product): ?EntryInterface
    {
        if (!$product instanceof PriceAwareInterface) {
            return null;
        }

        return new PercentEntry($this->getTaskCompletenessValue($product));
    }

    /**
     * @return array<\Ibexa\Bundle\ProductCatalog\UI\Completeness\Task\TaskGroup>|null
     */
    public function getSubtaskGroups(ProductInterface $product): ?array
    {
        if (!$product instanceof PriceAwareInterface) {
            return null;
        }

        $enabledCurrencies = $this->getEnabledCurrencies();
        $definedPrices = $this->getDefinedPrices($product);
        $defaultGroup = new TaskGroup('default', 'Default', $product);

        foreach ($enabledCurrencies as $currency) {
            $defaultGroup->addTask(
                new PriceCurrencySubtask($currency, $this->router, $definedPrices)
            );
        }

        return [$defaultGroup];
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
        return null;
    }

    /**
     * @return \Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface[]
     */
    private function getEnabledCurrencies(): array
    {
        $currencies = [];
        $query = new CurrencyQuery(new IsCurrencyEnabledCriterion());

        $result = new BatchIterator(
            new CurrencyFetchAdapter($this->currencyService, $query),
            self::CURRENCIES_LOAD_BATCH_SIZE
        );

        foreach ($result as $currency) {
            $currencies[] = $currency;
        }

        return $currencies;
    }

    /**
     * @return \Ibexa\Contracts\ProductCatalog\Values\PriceInterface[]
     */
    private function getDefinedPrices(ProductInterface $product): array
    {
        return $this->priceService
            ->findPricesByProductCode($product->getCode())
            ->getPrices();
    }
}
