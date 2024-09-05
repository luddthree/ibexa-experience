<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\EventSubscriber;

use Ibexa\Bundle\ProductCatalog\EventSubscriber\CustomPriceQueryFilterSubscriber;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Elasticsearch\Query\Event\QueryFilterEvent;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Contracts\ProductCatalog\CustomerGroupResolverInterface;
use Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\ProductCriterionAdapter;
use Ibexa\Contracts\ProductCatalog\Values\Content\Query\SortClause\ProductSortClauseAdapter;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Aggregation\CustomPriceStatsAggregation;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause;
use Money\Money;
use PHPUnit\Framework\TestCase;

final class CustomPriceQueryFilterSubscriberTest extends TestCase
{
    public function testOnQueryFilterEvent(): void
    {
        $expectedCustomerGroup = $this->createMock(CustomerGroupInterface::class);

        $customerGroupResolver = $this->createMock(CustomerGroupResolverInterface::class);
        $customerGroupResolver->method('resolveCustomerGroup')->willReturn($expectedCustomerGroup);

        $currency = $this->createMock(CurrencyInterface::class);

        $criterion = new Criterion\CustomPrice(Money::EUR(100));
        $sortClause = new SortClause\CustomPrice($currency);
        $aggregation = new CustomPriceStatsAggregation('custom_price_stats', $currency);

        $query = new Query();
        $query->filter = new ProductCriterionAdapter($criterion);
        $query->sortClauses[] = new ProductSortClauseAdapter($sortClause);
        $query->aggregations[] = $aggregation;

        $subscriber = new CustomPriceQueryFilterSubscriber($customerGroupResolver);
        $subscriber->onQueryFilterEvent(new QueryFilterEvent($query, new LanguageFilter([], false, false)));

        self::assertEquals($expectedCustomerGroup, $criterion->getCustomerGroup());
        self::assertEquals($expectedCustomerGroup, $sortClause->getCustomerGroup());
        self::assertEquals($expectedCustomerGroup, $aggregation->getCustomerGroup());
    }
}
