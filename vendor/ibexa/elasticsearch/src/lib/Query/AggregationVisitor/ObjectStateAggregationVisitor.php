<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\AggregationVisitor;

use Ibexa\Contracts\Core\Persistence\Content\ObjectState\Handler as ObjectStateHandler;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\ObjectStateTermAggregation;
use Ibexa\Contracts\Elasticsearch\Query\AggregationVisitor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\TermsAggregation;

final class ObjectStateAggregationVisitor implements AggregationVisitor
{
    /** @var \Ibexa\Contracts\Core\Persistence\Content\ObjectState\Handler */
    private $objectStateHandler;

    public function __construct(ObjectStateHandler $objectStateHandler)
    {
        $this->objectStateHandler = $objectStateHandler;
    }

    public function supports(Aggregation $aggregation, LanguageFilter $languageFilter): bool
    {
        return $aggregation instanceof ObjectStateTermAggregation;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\ObjectStateTermAggregation $aggregation
     */
    public function visit(AggregationVisitor $dispatcher, Aggregation $aggregation, LanguageFilter $languageFilter): array
    {
        $qb = new TermsAggregation('object_state_id_mid');
        $qb->withSize($aggregation->getLimit());
        $qb->withMinDocCount($aggregation->getMinCount());
        $qb->withIncludeValues($this->getObjectStateIds($aggregation->getObjectStateGroupIdentifier()));

        return $qb->toArray();
    }

    private function getObjectStateIds(string $identifier): array
    {
        try {
            $objectStateGroup = $this->objectStateHandler->loadGroupByIdentifier($identifier);
        } catch (NotFoundException $e) {
            return [];
        }

        $ids = [];

        $objectStates = $this->objectStateHandler->loadObjectStates($objectStateGroup->id);
        foreach ($objectStates as $objectState) {
            $ids[] = $objectState->id;
        }

        return $ids;
    }
}

class_alias(ObjectStateAggregationVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\AggregationVisitor\ObjectStateAggregationVisitor');
