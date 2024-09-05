<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\AggregationVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\AbstractRangeAggregation;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;

final class DateRangeAggregationVisitor extends AbstractDateRangeAggregationVisitor
{
    /** @var string */
    private $aggregationClass;

    /** @var \Ibexa\Elasticsearch\Query\AggregationVisitor\AggregationFieldResolver */
    private $aggregationFieldResolver;

    public function __construct(string $aggregationClass, AggregationFieldResolver $aggregationFieldResolver)
    {
        $this->aggregationClass = $aggregationClass;
        $this->aggregationFieldResolver = $aggregationFieldResolver;
    }

    public function supports(Aggregation $aggregation, LanguageFilter $languageFilter): bool
    {
        return $aggregation instanceof $this->aggregationClass;
    }

    protected function getTargetField(AbstractRangeAggregation $aggregation): string
    {
        return $this->aggregationFieldResolver->resolveTargetField($aggregation);
    }
}

class_alias(DateRangeAggregationVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\AggregationVisitor\DateRangeAggregationVisitor');
