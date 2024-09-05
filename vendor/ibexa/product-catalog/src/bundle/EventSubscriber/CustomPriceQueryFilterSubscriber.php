<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\EventSubscriber;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\CompositeCriterion as ContentCompositeCriterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalOperator;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\CriterionInterface as ContentCriterionInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause;
use Ibexa\Contracts\Elasticsearch\Query\Event\QueryFilterEvent;
use Ibexa\Contracts\ProductCatalog\CustomerGroupResolverInterface;
use Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\ProductCriterionAdapter;
use Ibexa\Contracts\ProductCatalog\Values\Content\Query\SortClause\ProductSortClauseAdapter;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Aggregation\CustomPriceStatsAggregation;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\CompositeCriterion;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\CustomPrice;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\LogicalNot;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\LogicalOr;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause as ProductSortClause;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class CustomPriceQueryFilterSubscriber implements EventSubscriberInterface
{
    private CustomerGroupResolverInterface $customerGroupResolver;

    public function __construct(CustomerGroupResolverInterface $customerGroupResolver)
    {
        $this->customerGroupResolver = $customerGroupResolver;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            QueryFilterEvent::class => 'onQueryFilterEvent',
        ];
    }

    public function onQueryFilterEvent(QueryFilterEvent $event): void
    {
        $query = $event->getQuery();

        if ($query->filter !== null) {
            $this->visitContentCriterion($query->filter);
        }

        if ($query->query !== null) {
            $this->visitContentCriterion($query->query);
        }

        foreach ($query->sortClauses as $sortClause) {
            $this->visitContentSortClause($sortClause);
        }

        if (!empty($query->aggregations)) {
            $this->visitAggregations($query->aggregations);
        }
    }

    private function visitContentCriterion(ContentCriterionInterface $criterion): void
    {
        if ($criterion instanceof ProductCriterionAdapter) {
            $this->visitProductCriterion($criterion->getProductCriterion());
        } elseif ($criterion instanceof LogicalOperator) {
            $this->visitContentLogicalOperator($criterion);
        } elseif ($criterion instanceof ContentCompositeCriterion) {
            $this->visitContentCompositeCriterion($criterion);
        }
    }

    private function visitContentCompositeCriterion(ContentCompositeCriterion $criterion): void
    {
        $this->visitContentCriterion($criterion->criteria);
    }

    private function visitContentLogicalOperator(LogicalOperator $criterion): void
    {
        foreach ($criterion->criteria as $criterion) {
            $this->visitContentCriterion($criterion);
        }
    }

    public function visitProductCriterion(CriterionInterface $criterion): void
    {
        if ($criterion instanceof LogicalAnd) {
            $this->visitProductLogicalAnd($criterion);
        } elseif ($criterion instanceof LogicalOr) {
            $this->visitProductLogicalOr($criterion);
        } elseif ($criterion instanceof LogicalNot) {
            $this->visitProductLogicalNot($criterion);
        } elseif ($criterion instanceof CompositeCriterion) {
            $this->visitProductCompositeCriterion($criterion);
        } elseif ($criterion instanceof CustomPrice) {
            $this->visitProductCustomPriceCriterion($criterion);
        }
    }

    private function visitProductLogicalAnd(LogicalAnd $criterion): void
    {
        foreach ($criterion->getCriteria() as $child) {
            $this->visitProductCriterion($child);
        }
    }

    private function visitProductLogicalOr(LogicalOr $criterion): void
    {
        foreach ($criterion->getCriteria() as $child) {
            $this->visitProductCriterion($child);
        }
    }

    private function visitProductLogicalNot(LogicalNot $criterion): void
    {
        $this->visitProductCriterion($criterion->getCriterion());
    }

    private function visitProductCompositeCriterion(CompositeCriterion $criterion): void
    {
        $this->visitProductCriterion($criterion->getInnerCriteria());
    }

    private function visitProductCustomPriceCriterion(CustomPrice $criterion): void
    {
        if ($criterion->getCustomerGroup() === null) {
            $criterion->setCustomerGroup($this->customerGroupResolver->resolveCustomerGroup());
        }
    }

    private function visitContentSortClause(SortClause $sortClause): void
    {
        if ($sortClause instanceof ProductSortClauseAdapter) {
            $this->visitProductSortClause($sortClause->getSortClause());
        }
    }

    private function visitProductSortClause(ProductSortClause $sortClause): void
    {
        if ($sortClause instanceof ProductSortClause\CustomPrice && $sortClause->getCustomerGroup() === null) {
            $sortClause->setCustomerGroup($this->customerGroupResolver->resolveCustomerGroup());
        }
    }

    /**
     * @param iterable<\Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation> $aggregations
     */
    private function visitAggregations(iterable $aggregations): void
    {
        foreach ($aggregations as $aggregation) {
            if (!$aggregation instanceof CustomPriceStatsAggregation) {
                continue;
            }

            if ($aggregation->getCustomerGroup() === null) {
                $aggregation->setCustomerGroup($this->customerGroupResolver->resolveCustomerGroup());
            }
        }
    }
}
