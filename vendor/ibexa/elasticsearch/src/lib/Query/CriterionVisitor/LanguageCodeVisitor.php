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
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\BoolQuery;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\TermQuery;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\TermsQuery;

final class LanguageCodeVisitor implements CriterionVisitor
{
    public function supports(Criterion $criterion, LanguageFilter $languageFilter): bool
    {
        return $criterion instanceof Criterion\LanguageCode;
    }

    public function visit(CriterionVisitor $dispatcher, Criterion $criterion, LanguageFilter $languageFilter): array
    {
        $value = (array)$criterion->value;
        if ($criterion->matchAlwaysAvailable) {
            $query = new BoolQuery();
            $query->addShould(new TermQuery('content_always_available_b', true));
            $query->addShould(new TermsQuery('content_language_codes_ms', $value));
        } else {
            $query = new TermsQuery('content_language_codes_ms', $value);
        }

        return $query->toArray();
    }
}

class_alias(LanguageCodeVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\CriterionVisitor\LanguageCodeVisitor');
