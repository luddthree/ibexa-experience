<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\ElasticSearch\Aggregation\ResultExtractor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult;
use Ibexa\Contracts\Elasticsearch\Query\AggregationResultExtractor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductList\AggregationResult\PriceStatsAggregationResult;
use Money\Currency;
use Money\Money;

final class PriceStatsAggregationResultExtractor implements AggregationResultExtractor
{
    /** @phpstan-var class-string<\Ibexa\Contracts\ProductCatalog\Values\Product\Query\Aggregation\AbstractPriceStatsAggregation> */
    private string $aggregationClass;

    /**
     * @phpstan-param class-string<\Ibexa\Contracts\ProductCatalog\Values\Product\Query\Aggregation\AbstractPriceStatsAggregation> $aggregationClass
     */
    public function __construct(string $aggregationClass)
    {
        $this->aggregationClass = $aggregationClass;
    }

    public function supports(Aggregation $aggregation, LanguageFilter $languageFilter): bool
    {
        return $aggregation instanceof $this->aggregationClass;
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Aggregation\AbstractPriceStatsAggregation $aggregation
     * @param array<string, mixed> $data
     */
    public function extract(
        Aggregation $aggregation,
        LanguageFilter $languageFilter,
        array $data
    ): AggregationResult {
        $currency = new Currency($aggregation->getCurrency()->getCode());

        return new PriceStatsAggregationResult(
            $aggregation->getName(),
            $data['count'] ?? null,
            array_key_exists('min', $data) ? new Money((int)$data['min'], $currency) : null,
            array_key_exists('max', $data) ? new Money((int)$data['max'], $currency) : null,
            array_key_exists('avg', $data) ? new Money((int)round($data['avg']), $currency) : null,
            array_key_exists('sum', $data) ? new Money((int)$data['sum'], $currency) : null,
        );
    }
}
