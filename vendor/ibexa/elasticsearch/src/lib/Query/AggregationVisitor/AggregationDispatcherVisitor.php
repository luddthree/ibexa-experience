<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\AggregationVisitor;

use Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Elasticsearch\Query\AggregationVisitor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;

final class AggregationDispatcherVisitor implements AggregationVisitor
{
    /** @var \Ibexa\Contracts\Elasticsearch\Query\AggregationVisitor[] */
    private $visitors;

    public function __construct(iterable $visitors)
    {
        $this->visitors = $visitors;
    }

    public function supports(Aggregation $aggregation, LanguageFilter $languageFilter): bool
    {
        return $this->findVisitor($aggregation, $languageFilter) !== null;
    }

    public function visit(
        AggregationVisitor $dispatcher,
        Aggregation $aggregation,
        LanguageFilter $languageFilter
    ): array {
        $visitor = $this->findVisitor($aggregation, $languageFilter);

        if ($visitor === null) {
            throw new NotImplementedException(
                'No visitor available for: ' . get_class($aggregation)
            );
        }

        return $visitor->visit($this, $aggregation, $languageFilter);
    }

    private function findVisitor(Aggregation $aggregation, LanguageFilter $languageFilter): ?AggregationVisitor
    {
        foreach ($this->visitors as $visitor) {
            if ($visitor->supports($aggregation, $languageFilter)) {
                return $visitor;
            }
        }

        return null;
    }
}

class_alias(AggregationDispatcherVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\AggregationVisitor\AggregationDispatcherVisitor');
