<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\CriterionVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Elasticsearch\Query\CriterionVisitor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Contracts\Elasticsearch\Repository\Values\Content\Query\RandomScore;
use stdClass;

final class RandomScoreVisitor implements CriterionVisitor
{
    public function supports(Criterion $criterion, LanguageFilter $languageFilter): bool
    {
        return $criterion instanceof RandomScore;
    }

    /**
     * @param \Ibexa\Contracts\Elasticsearch\Repository\Values\Content\Query\RandomScore $criterion
     */
    public function visit(CriterionVisitor $dispatcher, Criterion $criterion, LanguageFilter $languageFilter): array
    {
        $randomScore = new stdClass();
        if ($criterion->hasSeed()) {
            $randomScore->seed = $criterion->getSeed();
            $randomScore->field = '_seq_no';
        }

        return [
            'function_score' => [
                'query' => $dispatcher->visit(
                    $dispatcher,
                    $criterion->getCriterion(),
                    $languageFilter
                ),
                'random_score' => $randomScore,
            ],
        ];
    }
}

class_alias(RandomScoreVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\CriterionVisitor\RandomScoreVisitor');
