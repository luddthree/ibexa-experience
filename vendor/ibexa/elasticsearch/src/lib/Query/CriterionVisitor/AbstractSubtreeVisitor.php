<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\CriterionVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Subtree;
use Ibexa\Contracts\Elasticsearch\Query\CriterionVisitor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\BoolQuery;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\WildcardQuery;

abstract class AbstractSubtreeVisitor implements CriterionVisitor
{
    final public function supports(Criterion $criterion, LanguageFilter $languageFilter): bool
    {
        return $criterion instanceof Subtree;
    }

    final public function visit(CriterionVisitor $dispatcher, Criterion $criterion, LanguageFilter $languageFilter): array
    {
        $wildcards = [];
        foreach ($criterion->value as $value) {
            $wildcards[] = str_replace('/', '\\/', $value) . '*';
        }

        if (count($wildcards) === 1) {
            return (new WildcardQuery($this->getTargetField(), reset($wildcards)))->toArray();
        }

        $qb = new BoolQuery();
        foreach ($wildcards as $wildcard) {
            $qb->addShould(new WildcardQuery($this->getTargetField(), $wildcard));
        }

        return $qb->toArray();
    }

    abstract protected function getTargetField(): string;
}

class_alias(AbstractSubtreeVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\CriterionVisitor\AbstractSubtreeVisitor');
