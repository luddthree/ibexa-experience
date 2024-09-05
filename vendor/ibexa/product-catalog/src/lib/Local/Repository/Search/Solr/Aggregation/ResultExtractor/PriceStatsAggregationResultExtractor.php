<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\Solr\Aggregation\ResultExtractor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductList\AggregationResult\PriceStatsAggregationResult;
use Ibexa\Contracts\Solr\ResultExtractor\AggregationResultExtractor;
use Money\Currency;
use Money\Money;
use stdClass;

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

    /**
     * @param string[] $languageFilter
     */
    public function canVisit(Aggregation $aggregation, array $languageFilter): bool
    {
        return $aggregation instanceof $this->aggregationClass;
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Aggregation\AbstractPriceStatsAggregation $aggregation
     * @param string[] $languageFilter
     */
    public function extract(Aggregation $aggregation, array $languageFilter, stdClass $data): AggregationResult
    {
        $currency = new Currency($aggregation->getCurrency()->getCode());

        return new PriceStatsAggregationResult(
            $aggregation->getName(),
            $data->count ?? null,
            isset($data->min) ? new Money((int)$data->min, $currency) : null,
            isset($data->max) ? new Money((int)$data->max, $currency) : null,
            isset($data->avg) ? new Money((int)round($data->avg), $currency) : null,
            isset($data->sum) ? new Money((int)$data->sum, $currency) : null,
        );
    }
}
